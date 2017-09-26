#COPY OF SIMULATION.PY CONVERTED TO PYTHON 3


#This is a file for a basic simulation using the LinkedCar model
#the *current* assumtions of this model are that each driver:
#       -reacts with a delay 
#       -The reaction is such that optimal speed for spacing and spacing for 
#        speed is every cars objective. THis is represented in the acceleration
#        of the car. a = (dp/p)(dv - v) where dp is desired position based on 
#        the velocity and dv is the desired velocity based on the position.
#        I think of the two terms as urgency severity, though this is a 
#        simplification. 


#To begin I will compute each cars actions in succession, instead of updating 
#everycar every second. This is possible because every car only looks ahead

#important conversion: 1 mph = 1.47 fps
#maximum acceleration for car ~4m/s^2 ~ 13 ft/s^2

#import sys
# Add the ptdraft folder path to the sys.path list
#sys.path.append('./../traffic/Lib/site-packages/')

from Cars_3 import CarChain
#import display
#import MySQLdb
import json


class Simulation:
    
    #Time is the number of miliseconds the simulation will run
    def __init__(self, time = 10*1000):
        self.time = time
        self.chain = None
        #store input information of car1
        self.car1 = [[0,0,0]] * time


    #create a chain of cars to simulate traffic with specified 
    #characteristics (speed in fps)
    def createChain(self, size = 10, init_speed = 60, spacing = 0):
        self.chain = CarChain(self.time, size, init_speed, spacing)
    
    
    #function for changing speed, up is a Boolean (true is acc. false is 
    #decel) start_time and end_time are in ms, and amount is in fps
    def accelerate(self, arr, up, start_time, end_time, amount):
        #save start_time
        st = arr[start_time]
        total_time = end_time-start_time
        
        #determine sign of acceleration
        sign = 1.0 if up == 1 else -1.0
        
        #calculate the acceleration in feet per ms^2
        acc = sign*amount/1000.0/total_time
        
        for i in range(start_time+1, end_time):
            arr[i] = [0, st[1] + 1000*(i-start_time)*acc, acc*1000000]
        
        for i in range(end_time, self.time):
            arr[i] = [0, arr[i-1][1], 0]
        
        return arr
    
    def setCar1(self, arr = []):
        #re-initialize the first cars array
        self.car1 = arr
        #change value from passed array if the passed array does not have 
        #the correct number of points
        if len(arr) != self.time:
            arr = [[0, self.chain.head.speed, 0]] * self.time
            self.car1 = self.accelerate(arr, 0, 2000, 6000, 30)
            self.car1 = self.accelerate(self.car1, 1, 10000, 14000, 30)
    
    
    #this is the method for updates a car's data
    def update(self, car):
        init_speed = car.speed
        init_pos = car.dist_to_next
        #iterate through all times in the simulation
        print(len(car.data_arr))
        for i in range(len(car.data_arr)):
            if i != 0:
                #update position and velocity based on previous values
                pos = car.data_arr[i-1][0] + (car.prev.data_arr[i-1][1] - 
                    car.data_arr[i-1][1])*.001
                vel = car.data_arr[i-1][1] + car.data_arr[i-1][2]*.001
                if vel < 0:
                    vel = 0
                acc = 0
                if i > car.rt:
                    #formulas for position, desired position (based on velocity)
                    #velocity and desired velocity (based on position)
                    P = (car.data_arr[i-car.rt][0])
                    DP = (car.data_arr[i-car.rt][1]*1.3/1.47)
                    V = (car.data_arr[i-car.rt][1])
                    DV = (car.data_arr[i-car.rt][0]*1.47/1.3)
                    #this formula is the crux of the problem
                    #it determines how the car will respond 
                    #alternatives are stored at the bottom of this file
                    acc = (DP/P)*(DV-V)  
                    #Im playing with a constant multiplier
                    acc *= 1.5
                    
                    #no acceleration if car is nearing next car
                    if (car.data_arr[i-car.rt][1] > car.prev.data_arr[i-car.rt][1]):
                        if acc > 0:
                            acc = (DP/P)*(car.prev.data_arr[i-car.rt][1]-V)
                    #I place ballpark limits on acc and dec based on current
                    #standards for cars
                    if acc > 13:
                        acc = 13
                    if acc < -26:
                        acc = -26
                    
                car.data_arr[i] = [pos, vel, acc]
            else:
                car.data_arr[i] = [init_pos, init_speed, 0]        
                
    #This method computes the data_arr for every car at every moment by going
    #down the chain 
    def run(self):
        head = self.chain.head
        head.data_arr = self.car1
        n = head.next
        i = 2
        while(n):
            print("calculating car: " + str(i))
            self.update(n)
            n = n.next
            i += 1
        
        self.setCar1()
    
    #method for printing results of simulation
    def printResults(self):
        #print self.chain.fcPos()
        data = self.chain.printChain(500)
        arr = [0]*len(data)
        for i, val in enumerate(data):
            arr[i] = val[5]
        print(arr)
        #display.plotBars(data)


    #method for creating DB file for use in webpage
    def exportToMySQL(self):
        #get complete data in matrix format
        complete_data = self.chain.printChain(1)
        head_pos = self.chain.fcPos()
        if len(head_pos) != len(complete_data):
            return "Array Size Error"
        #create MySQL client
        db = MySQLdb.connect (host="localhost", user = "root", passwd = "", db = "traffic_db")
        cursor = db.cursor()
        for i, time in enumerate(complete_data):
            if type(time) == list:
                cursor.execute("delete from times where ms_time = " + str(i+1))
                cursor.execute("insert into times (ms_time, head_pos, Car1, Car2, " + 
                "Car3, Car4, Car5, Car6, Car7, Car8, Car9, Car10) values("+str(i+1) + ", "+ str(head_pos[i])
                + ", "+ str(time[0]) + ", "+ str(time[1]) + ", "+ str(time[2]) + ", "+ str(time[3]) + ", "
                + str(time[4]) + ", "+ str(time[5]) + ", "+ str(time[6]) + ", "+ str(time[7]) + 
                ", "+ str(time[8]) + ", "+ str(time[9]) + ")")
        db.commit()
        db.close()
    
    def exportToJSON(self):
        complete_data = self.chain.printChain(1)
        head_pos = self.chain.fcPos()
        if len(head_pos) != len(complete_data):
            return "Array Size Error"
        
        #create JSON table
        json_table = [0]*len(complete_data)
        for i, time in enumerate(complete_data):
            json_table[i] = {'ms_time': i+1, 'head_pos' : head_pos[i], 'Car1' : time[0], 
            'Car2' : time[1], 'Car3' : time[2], 'Car4' : time[3], 'Car5' : time[4],
            'Car6' : time[5], 'Car7' : time[6], 'Car8' : time[7], 'Car9' : time[8],
            'Car10' : time[9]}
        
        with open('./simulations/trial.json', 'w') as outfile:
            json.dump(json_table, outfile)
            
                
    

def createAndRun(time, change, st, et, am):
    #creates a path for the first car and runs the simulation
    #exports the data to a json file (refer to exportToJSON for filename)
    sim = Simulation(time)
    sim.createChain()
    
    #cv = change value which indicated the direction of change (acc or dec)
    if type(st) == list:
        cv = [0] * len(st)
    else:
        cv = [0]
        st = [st]
        et = [et]
        am = [am]
        
        
    for i in range(len(cv)):
        if change[i] == 'acc':
            cv[i] = 1
    
    #create initial path with not acceleration and constant speed
    arr = [[0, sim.chain.head.speed, 0]] * time
    
    #set initial path based on input values
    for i in range(len(cv)):
        arr = sim.accelerate(arr, cv[i], int(st[i]), int(et[i]), int(am[i]))
    sim.setCar1(arr)
    
    #sun simulation
    sim.run()
    
    #export data to JSON file
    sim.exportToJSON()



def main():
    #create new simulation
    sim = Simulation(30000)
    #initialize chain of cars
    sim.createChain()
    #initialize path for first car, when passed with no arguments, uses an array
    #created using the accelerate method in the setCar1 function
    sim.setCar1()
    #running the simulation updates the data_arr for every car after the head 
    #iteratively
    sim.run()
    #print sim.chain.head.next.next.next.next.next.next.next.next.next.data_arr
    #sim.exportToMySQL()
    sim.exportToJSON()
    #sim.printResults()


if __name__ == "__main__":
    main()
    