早年使用zend framework开发的时候非常爽，各种类库非常齐全，官方文档介绍也很详细。但是当访问量上来的时候，每次到晚上服务器负载就直飙上来导致服务器挂掉。后来和大家一起去找原因，才发现原来zend framework性能非常低。一开始的时候尝试把访问频繁的接口进行裸写，但是这只是临时做法，长远来看没有框架的约束后续的开发就会越来越乱。

经过好友@老雷的介绍认识yaf框架，了解了一下yaf是c语言写的，测试了下hello world的性能确实非常快，和原生的php差不多。yaf的目录结构和zend framework又非常接近，开发人员迁移过来也很方便。后来就尝试在项目中使用yaf，确实非常不错，然后就一直用到现在。

备注：这里说的zend框架指的是1，zend 2在目录结构上变化很大，这里就不讨论了。关于zend framework和yaf等框架的性能测试，上次看到老外的一篇文章对各种php框架做了比较，可以参考下：http://www.ruilog.com/blog/view/b6f0e42cf705.html

虽然yaf性能很快，但是缺少诸如表单、数据库操作等类库的封装，在开发上不免带来不便。在长期的开发中自己封装了一些类库，总结了一套开发的想法分享出来。

当前更新进度：正在整理中，后续会陆续整理上来。

1、php yaf框架扩展实践一——配置篇

主要说明了应用中如何进行配置可以更好的适应开发环境、测试环境和生产环境。

2、php yaf框架扩展实践二——多模块

一个项目配置多模块是否是合适？什么时候配置多模块比较好？

3、php yaf框架扩展实践三——表单

讲解了项目中对表单的封装和表单类的使用。

4、php yaf框架扩展实践四——业务层

说明了业务层的开发思想，涉及app的api接口开发管理和业务流程中断。

5、php yaf框架扩展实践五——数据层

讲解数据层的封装思想和相应类的使用。

6、php yaf框架扩展实践六——单元测试、计划任务、第三方库等

说明下yaf框架下如果实现单元测试，计划任务的规划部署和第三方库的导入使用等。