# 将微信小程序转换为百度智能小程序

#### 说明：
* 修改convert_wx_to_bd.php中的小程序所在目录，运行即可自动转换
* 只修改了基本的不同部分，其他需要修改之处待完善

#### 修改文件后缀名
* .wxml转换为.swan
* .wxss转换为.css

#### 修改文件内容
* .js文件：将wx.替换为swan.
* .swan文件：
* 循环部分：
* wx:for="{{var}}替换为s-for="var"
* wx:key替换为s-for-index
* wx:for-item替换为s-for-item
* 条件部分：
* wx:if="{{expression}}替换为s-if="expression"
* wx:elif="{{expression}}替换为s-elif="expression"
* wx:else替换为s-else
* 模板：<template is="var" data="{{{var}}}" />需将data属性两个大括号替换为三个大括号
