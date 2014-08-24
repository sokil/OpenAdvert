#!/bin/bash

# config
cssFilesDir=./../public/css/
compiledCssFilename=main.css

# init css files array
css=(
    bootstrap.min.css
    ui.css
    pickmeup.min.css
)

# create or clear compiled css file
compiledCssFile=${cssFilesDir}${compiledCssFilename}
cat < /dev/null > $compiledCssFile

# compile css files
for i in ${css[@]}
do
    cssFile=$cssFilesDir$i
    echo Complining $cssFile
    java -jar yuicompressor-2.4.7.jar --type css $cssFile >> $compiledCssFile
done
