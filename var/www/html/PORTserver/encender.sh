#!/bin/bash

lxc start $1

lxc file push /home/encenderdentro.sh $1/root/

tmux new-session -d -s $RANDOM lxc exec $1 -- bash /root/encenderdentro.sh -d
