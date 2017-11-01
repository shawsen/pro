#!/bin/bash
####################################################
# @file:   build.sh
# @author: mawentao
# @create: 2017-10-28 13:45:37
# @modify: 2017-10-28 13:45:37
# @brief:  build.sh
####################################################

pluginname="ebid"
outdir="output/$pluginname"
tarname="$pluginname.zip"
src="src-"`date +%s`

function cpfiles()
{
    for i in $@; do
        cp -r $i $outdir
    done
}

################################
rm -rf output
mkdir -p $outdir
################################
cpfiles api conf *.php *.xml class table model template
################################
mv $outdir/template/src $outdir/template/$src
sed -i "s/src\//$src\//g" $outdir/template/frame.html
sed -i "s/mwt3.2utf8 (http:\/\/10.3.70.15:8008\/discuz\/)/dz3.2utf8 (http:\/\/192.168.0.1\/dz)/g" $outdir/discuz_plugin_ebid.xml
sed -i "s/X3.2/X2.5,X3,X3.1,X3.2/g" $outdir/discuz_plugin_ebid.xml
################################
cd $outdir
# 删除php文件中的所有注释代码
../../clear_annotation -r -w
iconv -f UTF-8 -t GBK discuz_plugin_ebid.xml > discuz_plugin_ebid_SC_GBK.xml
mv discuz_plugin_ebid.xml discuz_plugin_ebid_SC_UTF8.xml
find . -type d -name ".svn" | xargs rm -rf
find . -name "*.bk" | xargs rm -rf
cd ../; zip -r $tarname $pluginname
cd ../
################################

echo 'build success'
exit 0
