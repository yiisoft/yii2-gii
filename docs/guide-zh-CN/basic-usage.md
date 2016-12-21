基本用法
===========

打开 Gii 时，首先进入到引导页，此处可以选择一种代码生成器。

![Gii entry page](images/gii-entry.png)

默认有以下生成器可用：

- **Model Generator** - 此生成器为指定的数据库表生成 ActiveRecord 类。
- **CRUD Generator** - 此生成器生成控制器和视图，实现指定数据模型的 CRUD（创建，读取，更新，删除）操作。
- **Controller Generator** - 此生成器可帮助您快速生成新的控制器类，生成一个或多个控制器操作及其对应的视图。
- **Form Generator** - 此生成器生成视图脚本文件，该脚本文件显示用于指定模型类的输入的表单。
- **Module Generator** - 此生成器可帮助您生成 Yii 模块所需的基础代码及结构。
- **Extension Generator** - 此生成器可帮助您生成Yii扩展所需的文件。

通过单击 "Start" 按钮选择生成器后，将看到一个表单，允许配置生成器的参数。 根据需要填写表单，然后按 "Preview" 按钮获取 Gii 将要生成的代码的预览。 根据选择的生成器以及文件是否已经存在，将得到类似于下图所示的输出：

![Gii preview](images/gii-preview.png)

单击文件名可以查看将为该文件生成的代码的预览。
当文件已经存在时，Gii 还提供了一个 diff 视图，它显示了存在的代码和将要生成的代码之间的区别。 在这种情况下，还可以选择应覆盖哪些文件，哪些不覆盖。

> 提示：在使用模型生成器在数据库更改后更新模型时，可以从 Gii 预览复制代码，并将更改与您自己的代码合并。可以使用IDE功能，如 PHPStorms [compare with clipboard（与剪贴板比较）](http://www.jetbrains.com/phpstorm/webhelp/comparing-files.html)，[Aptana Studio](http://www.aptana.com/products/studio3/download) 或者使用 [Eclipse](http://www.eclipse.org/pdt/) 通过 [AnyEdit tools plugin](http://andrei.gmxhome.de/anyedit/) 也允许 [compare with clipboard（与剪贴板比较）](http://andrei.gmxhome.de/anyedit/examples.html)，这样可以合并相关的更改，并省略其他可能被自己还原的代码。

在检查代码并选择要生成的文件后，可以单击 "Generate" 按钮创建文件即可。 如果看到Gii无法生成文件的错误时，则需调整目录权限，以便的Web服务器能够写入目录并创建文件。

> 注意：Gii 生成的代码只是一个根据自己的需要进行调整的模板。 它可以帮助你快速创建新的代码，但它不是创建准备使用于正式项目的代码。我们经常看到人们使用 Gii 生成的 model 没有改变，只是通过扩展它们从而调整部分 model 的功能。 这不是我们编写它的初衷。 Gii 生成的代码可能不完整或不正确，必须根据自己的需要进行更改才能使用它。
  