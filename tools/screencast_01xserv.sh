#!/bin/sh

Xephyr -keybd ephyr,,,xkbmodel=evdev -br -reset -host-cursor -screen 768x576x24 -dpi 96 :2 &
XEPHPID=$!
sleep 2
export DISPLAY=:2.0
echo "starting Window manager in $DISPLAY"
cd $HOME
ion3 
kill $XEPHPID
