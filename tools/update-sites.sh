#!/bin/sh
for file in $(find . -name "*.php"); do
	if test "$file" == "./lib/quoted_printable.php"; then continue; fi;
	php -l $file || exit
done

yui-compressor --type js -o static/script.js static/script_src.js
yui-compressor --type css -o static/style.css static/style_src.css

git commit -a
echo -n "git push/pull ? [Enter|Ctrl-C]"

read || exit
git pull || exit
git push
if test "$(hostname)" == "soyuz"; then
  ssh rg42.org 'cd /var/sites/lac2014; git pull'
fi
ssh lac@linuxaudio.org 'cd /home/sites/lac.linuxaudio.org/2014/docroot; git pull'
