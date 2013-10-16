#!/bin/sh
yui-compressor --type js -o static/script.js static/script_src.js
yui-compressor --type css -o static/style.css static/style_src.css
