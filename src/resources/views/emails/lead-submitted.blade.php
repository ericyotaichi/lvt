<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>新的聯絡我們需求</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <h2>新的聯絡我們需求</h2>
    <p>以下是使用者提交的資訊：</p>
    <table cellpadding="6" cellspacing="0" border="0">
        <tr>
            <td><strong>方案：</strong></td>
            <td>{{ $product?->title ?? $lead->plan }}</td>
        </tr>
        <tr>
            <td><strong>姓名：</strong></td>
            <td>{{ $lead->name }}</td>
        </tr>
        <tr>
            <td><strong>電話：</strong></td>
            <td>{{ $lead->phone ?: '未填寫' }}</td>
        </tr>
        <tr>
            <td><strong>Email：</strong></td>
            <td>{{ $lead->email }}</td>
        </tr>
        <tr>
            <td valign="top"><strong>需求/備註：</strong></td>
            <td>{!! nl2br(e($lead->notes)) ?: '未填寫' !!}</td>
        </tr>
    </table>
</body>
</html>
