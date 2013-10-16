#!/bin/sh

DST=$HOME/Screencasts
DST=/tmp/


DATE=`date +%Y%m%d-%H%M`
export DISPLAY=:2.0

# start audio-recording
xterm -display :0.0 -e jack_capture -b 24 $DST/screencast_audio_$DATE.wav &

# start screencast-capture
ffmpeg -y -an -f x11grab -r 30 -s 800x600 -i :2 \
	-vcodec libx264 -vpre lossless_ultrafast -threads 2 \
	$DST/screencast_audio_$DATE.avi

killall jack_capture
