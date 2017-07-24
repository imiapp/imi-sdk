#### Android 接入说明

### 1 环境配置
  
步骤1：导入sdk相关的jar包，放在libs目录：bcprov-jdk15on-1.54，gson-2.5，  
sign-tool-1.0.0-RELEASE。  
步骤2：build.geadle文件中添加第三方依赖库: compile 'com.loopj.android:android-async-http:1.4.9'。  
步骤 3：使androidStudio支持java1.8特性（参考demo）。  
jackOptions {  
    enabled true  
}  
compileOptions {  
    sourceCompatibility JavaVersion.VERSION_1_8  
    targetCompatibility JavaVersion.VERSION_1_8  
}  
步骤 4：添加https访问证书,将vport.bks放到raw目录下。  
步骤 5：导入IMI sdkjar包。  

### 2 SDK API

方法一 ：登录（方法名为reqLogin）  
方法二 ：授权（方法名为reqAuthorize）  
方法三： 检查IMI的安装（方法名为isIMIInstalled）  

### 3 Demo

https://github.com/imiapp/android-demo
