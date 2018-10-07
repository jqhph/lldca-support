lldca-support

>静态资源加载工具配置说明
```php
/*
 | 静态资源帮助工具配置
 |
 */
'assets' => [
    // 静态资源域名配置(默认域名)
    'resource-server' => env('ASSETS_SERVER', '//default.com'),

    // js文件请求后缀
    'js-version' => '',

    // css文件请求后缀
    'css-version' => '',

    // 静态资源别名配置
    'alias' => [
        'test1' => '/assets/test1', // 不填写完整地址, 会使用"resource-server"参数配置的值作为域名
        'test2' => '//xxx.com/assets/test2', // 填写完整地址, 但是域名使用相对路径
        'test3' => 'https://xxx.com/assets/test3', // 填写完整地址
    ],
],

```
