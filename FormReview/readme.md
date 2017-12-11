## 安装
1. 环境要求：
  1. PHP >= 5.6
  2. Redis >= 2.8.4
  3. MongoDB >= 3.4.2
  4. Nginx >= 1.0.2
  5. MySQL >= 5.6.34

2. 解压压缩包

3. 复制.env.example文件为.env文件，配置.env文件，需要配置数据库，缓存，邮箱等信息

4. 数据库迁移
 ```
php artisan migrate
```

5. 运行
  -  临时运行直接运行
```
php artisan serve
```
  - 配置nginx，再运行

## 使用说明
1. 首页：

  ![首页](http://upload-images.jianshu.io/upload_images/4180122-436d49aa8f316833.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

2. 创建表单：
  填写表单信息，通过鼠标拖拽创建表单。

  ![](http://upload-images.jianshu.io/upload_images/4180122-2039b3d13ac696dd.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
  ![](http://upload-images.jianshu.io/upload_images/4180122-030e18103cbc7e3b.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

3. 表单首页
  各种按钮及时修改设置，数据统计
  ![表单首页](http://upload-images.jianshu.io/upload_images/4180122-4faa898a82f624fe.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

4. 管理员设置
  列表页可以查看管理员，删除管理员。邀请页可以根据条件邀请其他用户作为此表单管理员。
  ![管理员列表](http://upload-images.jianshu.io/upload_images/4180122-fbc7537408c1934a.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

  ![邀请管理员](http://upload-images.jianshu.io/upload_images/4180122-ac8a8a6377c1d867.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

5. 通知设置
  绑定微信服务号后可以设置微信模板消息通知。
  ![设置通知页](http://upload-images.jianshu.io/upload_images/4180122-cf779bf1bcf58306.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

6. 发布

  ![发布页](http://upload-images.jianshu.io/upload_images/4180122-1a9f241c47f5b4fa.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

7. 数据列表
  左上角按钮可以切换数据类型，右上角按钮可以刷新，筛选，导出数据等。
  ![数据列表](http://upload-images.jianshu.io/upload_images/4180122-cc9a214db948c63c.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

8. 报表
  仅对有选择的表单项进行报表统计。
![报表](http://upload-images.jianshu.io/upload_images/4180122-68685dce4b9e8433.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

9. 自定义样式
  ![自定义样式](http://upload-images.jianshu.io/upload_images/4180122-d683aa6c60fb46c8.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

