#!/bin/bash

# config
jsFilesDir=./../public/js/
compiledJsFilename=main.js

# init js files array
js=(
    bootstrap.min.js
    common.js

    form.js
    modal.js

    jquery.pickmeup.min.js

    bannerEditor.js
    videoBannerEditor.js
    imageBannerEditor.js
    textPopupBannerEditor.js
    creepingLineBannerEditor.js

    stat.js
)

# create or clear compiled js file
compiledJsFile=${jsFilesDir}${compiledJsFilename}
cat < /dev/null > $compiledJsFile

# compile js files
for i in ${js[@]}
do
    jsFile=$jsFilesDir$i
    echo Complining $jsFile
    java -jar yuicompressor-2.4.7.jar --type js $jsFile >> $compiledJsFile
done

