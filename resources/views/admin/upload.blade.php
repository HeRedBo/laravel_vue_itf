<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-cn">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <title>新网页标题</title>
    <meta name="keywords" content="关键字列表" />
    <meta name="description" content="网页描述" />
    <link rel="stylesheet" type="text/css" href="" />
    <style type="text/css"></style>
    <script type="text/javascript"></script>
</head>
<body>
<form method="POST" enctype="multipart/form-data" action="/admin/upload/upAvatar">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="">
    <input type="file"  name="avatar" >
    <input type="submit" value="上传">
</form>
</body>
</html>