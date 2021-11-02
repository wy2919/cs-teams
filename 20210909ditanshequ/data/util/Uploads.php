<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\util;


class Uploads
{

    /**
     * 获取上传文件的 文件名 和 存储路径
     * @return \think\response\Json
     */
    public function upfile(){
        $path = [];
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/uploads/ 目录下
        $names = explode('.',$_FILES['file']['name']);
        $name = $names[0].'.'.$names[count($names)-1];
        $info = $file->validate(['size'=>15678000])->move( './uploads');

        if($info){
            $getSaveName=str_replace("\\","/",$info->getSaveName());
            $path=[
                'suffix' => $info->getExtension(), //获取文件后缀格式
                'name' => $name,
                'path' => '/uploads/'.$getSaveName,
            ];
        }
        return json($path);
    }

}