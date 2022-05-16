#/bin/bash

WHO=`whoami`
echo "Hi," $WHO
echo " "

ls ./public 1> /dev/null 2> /dev/null

if [ "$?" == "0" ];
then
  #V1
  apidoc -c scripts/docfg/v1 -i app/Http/Controllers/ApiV1/ -o public/apidoc/app-v1
  #V2
  apidoc -c scripts/docfg/v2 -i app/Http/Controllers/Api -o public/apidoc/app-v2
  apidoc -c scripts/docfg/v2-web -i app/Http/Controllers/Web/Api -o public/apidoc/web-v2
else
  echo "You should run as: ./scripts/make-api-doc.sh"
fi

