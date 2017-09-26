#James Page
#9/19/2017
#This is a file that will be used for dispaying data to develop the correct
#algorithm


import matplotlib.pyplot as plt
import numpy as np


#method displays data as simple bar graph, each entry is a car
def plotBars(data):
    for i, time in enumerate(data):
        plt.bar(np.arange(len(time)), time, align = 'center')
        plt.show()