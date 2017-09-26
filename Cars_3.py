# -*- coding: utf-8 -*-
"""
Created on Sat Sep 23 15:54:54 2017

@author: James Page
"""

#COPY OF CARS.PY CONVERTED TO PYTHON 3

#James Page
#9/18/2017

#This is the first file for trying to derive the traffic wave equation from
#modeling a simulation based on typical human responses. In the future it could
#be remodeled to include a distribution of responses and variables.

#The final goal of the project will be to use Machine Learning to search for
#the underlying equation that can be confirmed using the known result. Can
#be found at www.wikiwaves.org/Traffic_Waves

#Ideally may also eventually be put into some observable format (web page)

#reaction time mean taken from:
#https://www.humanbenchmark.com/tests/reactiontime/statistics



#this is current model I use for a car on the road
class Car:
    
    def __init__(self, time, dist = 0, speed = 0):
        #dist is in feet, speed in fps
        self.dist_to_next = dist
        self.speed = speed
        self.acceleration = 0
        #reaction time in ms
        self.rt = 282
        #car size in feet
        self.length = 13.0
        self.next = None
        self.prev = None
        #every car has an 3xN matrix, this will include position, velocity, and
        #acceleration information at all times.
        self.data_arr = [[0,0,0]] * time
        


#this is the current model I use for traffic
#it is a LinkedList, which means that cars only care about what's in front of 
#them
class CarChain:
        
    #create chain of cars with size, initial_speed, and spacing as inputs
    def __init__(self, time, size = 10, init_speed = 1.47*50.0, spacing = 0):
        #there are a number of rules for spacing, I'm using 1 car length per
        #10 miles per hour, assuming 13 foot car averages, the 2 second rule is
        #roughly 2 car lengths per 10 miles per hour with 13 foot cars
        if spacing == 0:
            spacing = init_speed * 1.3 / 1.47
        
        #CLASS VARIABLES HERE
        self.size = size
        #create head car
        self.head = Car(time, 0, init_speed)
        
        #add remaining cars
        n = self.head
        for i in range(size-1):
            n.next = Car(time, spacing, init_speed)
            n.next.prev = n
            n = n.next
            
            
    #this is a method that is called to see how the absolute positions of the 
    #cars in the chain are changing. It takes the time (in ms) between prints 
    #as an input, default is half a second (21 prints for 10 seconds)
    def printChain(self, dt = 500):
        #save the size of the final array
        arr_size = int((len(self.head.data_arr)/dt))
        if dt != 1:
            #add one for final state if not included
            arr_size += 1
            
        pos_data = [0] * (arr_size)
        ind = 0
        for i in range(len(self.head.data_arr)):
            if i%dt == 0 or i == (len(self.head.data_arr)-1):
                pos_data[ind] = self.printChainHelper(i)
                ind += 1
        return pos_data
    
    #method that prints every car position relative to the first car or the
    #car in front of it (w/ or w/o dist = 0 in while loop) at specified index
    def printChainHelper(self, ind = 0):
        dist_list = [0] * self.size
        
        n = self.head
        car_ind = 0
        dist = 0
        #iterate through car line
        while(n):
            #update and record distances
            if ind == 0:
                dist += n.dist_to_next
            else:
                dist += n.data_arr[ind][0]
            
            dist_list[car_ind] = dist
            dist += n.length
            
            #iterate car and index
            car_ind += 1
            n = n.next
        
        return dist_list
    
    
    #this is a method for returning the distance traveled by the first car
    def fcPos(self):
        fc = [0] * len(self.head.data_arr)
        #iterate through car [pos,vel,acc] data to calculate position
        for i in range(1,len(self.head.data_arr)):
            fc[i] = fc[i-1] + self.head.data_arr[i-1][1]*(.001)
        
        return fc




def main():
    #CarChain takes 1,2, 3, or 4 inputs:
    #time, list size, initial_speed, car spacing (from back to front)
    smallChain = CarChain(10000)
    print(smallChain.printChain())
    
    
if __name__ == "__main__":
    main()
            
        