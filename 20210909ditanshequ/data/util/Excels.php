<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\util;

require $_SERVER['DOCUMENT_ROOT'].'/../extend/PHPExcel.php';
require $_SERVER['DOCUMENT_ROOT'].'/../extend/PHPExcel/IOFactory.php';

class Excels
{

    /*导出示例*/
    public function exportUserExce(){
        $xlsName  = "管理员信息表";// 表名

        $arr = session('xlsData');
        $xlsData = [];
        foreach ($arr as $v){
            $xlsData[] = $v; //二维数据  每一个元素就是一条数据   数据字段对应下面的对应关系
        }
        $xlsCell  = array( // 表头显示的标题
            array('account','会员名'),
            array('realname','中文名'),
            array('sex','性别'),
            array('tel','手机号码'),
            array('qq','QQ'),
            array('wechat','wechat'),
            array('role_name','role_name'),
            array('state','状态')
        );
        $this->exportExcels($xlsName,$xlsCell,$xlsData); //调用导出方法
    }


    /**
     * 导出 表格
     * @param $expTitle  表格文件名 标题
     * @param $expCellName  列表题
     * @param $expTableData  数据
     */
    public function exportExcels($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = date('Ymd'.$expTitle);//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);


        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle);
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
            }
        }

        ob_end_clean();//清除缓冲区,避免乱码
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output');
    }

    /**
     * 导出多个表格 sheet
     * @param $expTitle
     * @param $expCellName
     * @param $expTableData
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function exportExcels_more($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = date('Ymd'.$expTitle);//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData)-1;


        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');


        /** 缺省情况下,PHPExcel会自动创建第一个SHEET，其索引SheetIndex=0 */
        /** 设置 当前处于活动状态的SHEET 为PHPExcel自动创建的第一个SHEET */
        foreach($expTableData as $key => $item) {
            if($key !== 0) $objPHPExcel->createSheet();
            $objPHPExcel->setactivesheetindex($key);
            /** 设置工作表名称 */
            $objPHPExcel->getActiveSheet($key)->setTitle($item['bnum']);

            $objPHPExcel->getActiveSheet($key)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
            $objPHPExcel->setActiveSheetIndex($key)->setCellValue('A1', $expTitle.'-'.$item['bnum']);
            for($i=0;$i<$cellNum;$i++){
                $objPHPExcel->setActiveSheetIndex($key)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
            }
            // Miscellaneous glyphs, UTF-8

            for($i=0;$i<count($item);$i++){
                for($j=0;$j<$cellNum;$j++){
                    if(isset($item[$i][$expCellName[$j][0]])){
                        $objPHPExcel->getActiveSheet($key)->setCellValue($cellName[$j].($i+3), $item[$i][$expCellName[$j][0]]);
                    }

                }
            }
        }


        ob_end_clean();//清除缓冲区,避免乱码
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output');

    }



    /**
     * 导入订单
     */
    public function imorder(){
        //return json($_SERVER);
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/excel/ 目录下
        $info = $file->move( './excel');
        if($info) {
            $suffix = $info->getExtension();  //获取文件后缀格式
            $getSaveName = str_replace("\\", "/", $info->getSaveName());    //获取文件名称
            $path = $_SERVER['DOCUMENT_ROOT'].'/excel/' . $getSaveName;
            //判断哪种类型
            if ($suffix != 'xls' && $suffix != 'xlsx') {
                return ['文件格式不正确导致上传失败-_-!'];
//                $this->error('文件格式不正确导致上传失败-_-!');
            }
            if ($suffix == "xlsx") {
                $reader = \PHPExcel_IOFactory::createReader('Excel2007');
            } else {
                $reader = \PHPExcel_IOFactory::createReader('Excel5');
            }


            $objContent = $reader->load($path);
            $sheetContent = $objContent->getSheet(0)->toArray();
            unset($sheetContent[0]);
            return $sheetContent;
            foreach ($sheetContent as $k => $v) {
                /*业务处理*/
            }
        }
        return '上传失败';
    }

    /*获取表格数据*/
    public function getExcelData($suffix,$path){
        $path = $_SERVER['DOCUMENT_ROOT'].$path;
        //判断哪种类型
        if ($suffix != 'xls' && $suffix != 'xlsx') {
            return ['文件格式不正确导致上传失败-_-!'];
        }
        if ($suffix == "xlsx") {
            $reader = \PHPExcel_IOFactory::createReader('Excel2007');
        } else {
            $reader = \PHPExcel_IOFactory::createReader('Excel5');
        }

        $objContent = $reader->load($path);
        $sheetContent = $objContent->getSheet(0)->toArray();
        unset($sheetContent[0]);
        return $sheetContent;
    }


    /*获取中文 简介*/
    public function getdescription($str,$len=0){
        $cn = "/[\x{4e00}-\x{9fff}]+/u";
        preg_match_all($cn,$str,$return);
        $str = '';
        foreach ($return[0] as $v){
            $str .= $v;
        }
        if($len > 0){
            $str =  mb_substr($str,0,70,"utf-8");
        }

        return $str;
    }

    /*获取 缩略图*/
    public function getlitpic($str){
        $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern,$str,$match);
//        print_r($match[1][0]);die;
        if(isset($match[1][0])){
            return $match[1][0];
        }else{
            return '';
        }
    }



}