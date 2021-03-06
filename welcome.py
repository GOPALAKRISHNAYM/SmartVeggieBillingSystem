from __future__ import division
import time
import torch 
import torch.nn as nn
from torch.autograd import Variable
import numpy as np
import cv2 
from util import *
from darknet import Darknet
from preprocess import prep_image, inp_to_image
import pandas as pd
import random 
import argparse
import pickle as pkl
import sqlite3
from sqlite3 import Error
from tkinter import *

def OpenPro1():
    def get_test_input(input_dim, CUDA):
        img = cv2.imread("imgs/messi.jpg")
        img = cv2.resize(img, (input_dim, input_dim)) 
        img_ =  img[:,:,::-1].transpose((2,0,1))
        img_ = img_[np.newaxis,:,:,:]/255.0
        img_ = torch.from_numpy(img_).float()
        img_ = Variable(img_)
        
        if CUDA:
            img_ = img_.cuda()
        
        return img_
    
    def prep_image(img, inp_dim):
        """
        Prepare image for inputting to the neural network. 
        
        Returns a Variable 
        """
    
        orig_im = img
        dim = orig_im.shape[1], orig_im.shape[0]
        img = cv2.resize(orig_im, (inp_dim, inp_dim))
        img_ = img[:,:,::-1].transpose((2,0,1)).copy()
        img_ = torch.from_numpy(img_).float().div(255.0).unsqueeze(0)
        return img_, orig_im, dim
    
    def write(x, img):
        c1 = tuple(x[1:3].int())
        c2 = tuple(x[3:5].int())
        cls = int(x[-1])
        label = "{0}".format(classes[cls])
        print(label)
        write_db(label)
        color = random.choice(colors)
        cv2.rectangle(img, c1, c2,color, 1)
        t_size = cv2.getTextSize(label, cv2.FONT_HERSHEY_PLAIN, 1 , 1)[0]
        c2 = c1[0] + t_size[0] + 3, c1[1] + t_size[1] + 4
        cv2.rectangle(img, c1, c2,color, -1)
        cv2.putText(img, label, (c1[0], c1[1] + t_size[1] + 4), cv2.FONT_HERSHEY_PLAIN, 1, [225,255,255], 1);
        return img
    
    def write_db(label):
        conn = None
        try:
            conn = sqlite3.connect('C:\\yolo\\pytorch-yolo-v3\\db\\final_bill.db')
            print('connected to db macha')
            sql = ''' INSERT INTO bill(item_name,price,weight,amount)
                  VALUES(?,?,?,?) '''
            sql1= "SELECT * FROM rate WHERE item_name = ?"
            sql2= "SELECT * FROM weights WHERE item_name = ?"
            cur = conn.cursor()
            q=[label]
            result = cur.execute(sql1,q)
            records = cur.fetchall()
            gk=0
            for row in records:
                print("price = ", row[1])
                gk=row[1]
            print(result)
            result = cur.execute(sql2,q)
            weights=cur.fetchall()
            mk=0
            for rows in weights:
                print("weight = ", rows[1])
                mk=rows[1]
            print(result)
            amt=gk*mk
            p = [label,gk,mk,amt]
            cur.execute(sql,p)
            conn.commit()
            conn.close()
            print('inserted')
        except Error as e:
            print(e)
        
    def arg_parse():
        """
        Parse arguements to the detect module
        
        """
        
        
        parser = argparse.ArgumentParser(description='YOLO v3 Cam Demo')
        parser.add_argument("--confidence", dest = "confidence", help = "Object Confidence to filter predictions", default = 0.25)
        parser.add_argument("--nms_thresh", dest = "nms_thresh", help = "NMS Threshhold", default = 0.4)
        parser.add_argument("--reso", dest = 'reso', help = 
                            "Input resolution of the network. Increase to increase accuracy. Decrease to increase speed",
                            default = "160", type = str)
        return parser.parse_args()
    
    cfgfile = "cfg/yolov3.cfg"
    weightsfile = "yolov3.weights"
    num_classes = 80
    
    args = arg_parse()
    confidence = float(args.confidence)
    nms_thesh = float(args.nms_thresh)
    start = 0
    CUDA = torch.cuda.is_available()
        
    
        
        
    num_classes = 80
    bbox_attrs = 5 + num_classes
        
    model = Darknet(cfgfile)
    model.load_weights(weightsfile)
        
    model.net_info["height"] = args.reso
    inp_dim = int(model.net_info["height"])
        
    assert inp_dim % 32 == 0 
    assert inp_dim > 32
    
    if CUDA:
        model.cuda()
                
    model.eval()
        
    videofile = 'video.avi'
        
    cap = cv2.VideoCapture(0)
        
    assert cap.isOpened(), 'Cannot capture source'
        
    frames = 0
    start = time.time()    
    while cap.isOpened():
            
        ret, frame = cap.read()
        if ret:
                
            img, orig_im, dim = prep_image(frame, inp_dim)
                
            im_dim = torch.FloatTensor(dim).repeat(1,2)                        
                
                
            if CUDA:
                im_dim = im_dim.cuda()
                img = img.cuda()
                
                
            output = model(Variable(img), CUDA)
            output = write_results(output, confidence, num_classes, nms = True, nms_conf = nms_thesh)
    	    #print(output)
    
            if type(output) == int:
                frames += 1
                print("FPS of the video is {:5.2f}".format( frames / (time.time() - start)))
                cv2.imshow("frame", orig_im)
                key = cv2.waitKey(1)
                if key & 0xFF == ord('q'):
                    break
                continue
                
    
            
            output[:,1:5] = torch.clamp(output[:,1:5], 0.0, float(inp_dim))/inp_dim
                
    #            im_dim = im_dim.repeat(output.size(0), 1)
            output[:,[1,3]] *= frame.shape[1]
            output[:,[2,4]] *= frame.shape[0]
    
                
            classes = load_classes('data/coco.names')
            colors = pkl.load(open("pallete", "rb"))
                
            list(map(lambda x: write(x, orig_im), output))
                
                
            cv2.imshow("frame", orig_im)
            key = cv2.waitKey(1)
            if key & 0xFF == ord('q'):
                break
            frames += 1
            print("FPS of the video is {:5.2f}".format( frames / (time.time() - start)))
    
                
        else:
            break
root = Tk()
button_1 = Button(root, text = "click me", command = OpenPro1)
button_1.pack()


root.mainloop()