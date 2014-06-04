#!/bin/sh

touch /home/congela/public_html/Erp/cron/log.txt

rm -Rf /home/congela/public_html/Erp/docs/docs001/pdfs/*
rm -Rf /home/congela/public_html/Erp/docs/docs001/tmp/*
rm -Rf /home/congela/public_html/Erp/docs/docs001/xls/*
rm -Rf /home/congela/public_html/Erp/docs/docs001/xml/*
rm -Rf /home/congela/public_html/Erp/docs/docs001/yml/*
echo "Temporales docs001" >> /home/congela/public_html/Erp/cron/log.txt

rm -Rf /home/congela/public_html/Erp/docs/docs002/pdfs/*
rm -Rf /home/congela/public_html/Erp/docs/docs002/tmp/*
rm -Rf /home/congela/public_html/Erp/docs/docs002/xls/*
rm -Rf /home/congela/public_html/Erp/docs/docs002/xml/*
rm -Rf /home/congela/public_html/Erp/docs/docs002/yml/*
echo "Temporales docs002" >> /home/congela/public_html/Erp/cron/log.txt
