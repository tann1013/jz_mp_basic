<?php
/**
 * Api路由
 */

use think\facade\Route;

Route::group('miniprogram', function () {
    Route::group('Index', [
        'index' => [
            'miniprogram/Index/index',
            ['method' => 'get']
        ],
        'wxlogin' => [
            'miniprogram/Index/wxlogin',
            ['method' => 'get']
        ]
    ]);

    Route::group('AccountBooks', [
        'addNormal' => [
            'miniprogram/AccountBooks/addNormal',
            ['method' => 'post']
        ],
        'getList' => [
            'miniprogram/AccountBooks/getList',
            ['method' => 'post']
        ],
        'addUserToAb' => [
            'miniprogram/AccountBooks/addUserToAb',
            ['method' => 'post']
        ],
        'out' => [
            'miniprogram/AccountBooks/out',
            ['method' => 'post']
        ]
    ])->middleware(['MiniProgramAuth']);
    Route::rule(
        'AccountBooks/removeUserByUid',
        'miniprogram/AccountBooks/removeUserByUid',
        'post'
    )->middleware(['MiniProgramAuth', 'MiniProgramCheckAbAdmin']);
    Route::rule(
        'AccountBooks/changeAdmin',
        'miniprogram/AccountBooks/changeAdmin',
        'post'
    )->middleware(['MiniProgramAuth', 'MiniProgramCheckAbAdmin']);
    Route::rule(
        'AccountBooks/getById',
        'miniprogram/AccountBooks/getById',
        'post'
    )->middleware(['MiniProgramAuth', 'MiniProgramCheckAbOwner']);
    Route::rule(
        'AccountBooks/getUsers',
        'miniprogram/AccountBooks/getUsers',
        'post'
    )->middleware(['MiniProgramAuth', 'MiniProgramCheckAbOwner']);
    Route::rule(
        'AccountBooks/getShareKey',
        'miniprogram/AccountBooks/getShareKey',
        'post'
    )->middleware(['MiniProgramAuth', 'MiniProgramCheckAbOwner']);
    //获取分类
    Route::group('Category', [
        'getList' => [
            'miniprogram/Category/getList',
            ['method' => 'post']
        ],
        'getUnTypeList' => [
            'miniprogram/Category/getUnTypeList',
            ['method' => 'post']
        ]
    ])->middleware(['MiniProgramAuth']);
    //-----------获取记录---------------start
    Route::group('Record', [
        'add' => [
            'miniprogram/Record/add',
            ['method' => 'post']
        ],
        'getList' => [
            'miniprogram/Record/getList',
            ['method' => 'post']
        ],
        'getTotalWithMonth' => [
            'miniprogram/Record/getTotalWithMonth',
            ['method' => 'post']
        ]
    ])->middleware(['MiniProgramAuth', 'MiniProgramCheckAbOwner']);

    //    根据id获取记账
    Route::rule(
        'Record/getById', 'miniprogram/Record/getById', 'post'
    )->middleware(['MiniProgramAuth']);
    Route::rule(
        'Record/edit', 'miniprogram/Record/edit', 'post'
    )->middleware(['MiniProgramAuth', 'MiniProgramCheckRecordOwner']);
    Route::rule(
        'Record/del', 'miniprogram/Record/del', 'post'
    )->middleware(['MiniProgramAuth', 'MiniProgramCheckRecordOwner']);
    //--------获取记录-------------end

    //--------资产操作-------------start
    Route::group('Account', [
        'getBalance' => [
            'miniprogram/Account/getBalance',
            ['method' => 'post']
        ],
        'balanceEdit' => [
            'miniprogram/Account/balanceEdit',
            ['method' => 'post']
        ]
    ])->middleware(['MiniProgramAuth']);
    //--------资产操作-------------end

    //--------统计操作-------------start
    Route::group('Total', [
        'getTotlaByMonth' => [
            'miniprogram/Total/getTotlaByMonth',
            ['method' => 'post']
        ],
        'getTotlaByYear' => [
            'miniprogram/Total/getTotlaByYear',
            ['method' => 'post']
        ]
    ])->middleware(['MiniProgramAuth','MiniProgramCheckAbOwner']);
    //--------统计操作-------------end

    //MISS路由定义
    Route::miss('miniprogram/Index/index');
})->middleware(['MiniProgramResponse']);

