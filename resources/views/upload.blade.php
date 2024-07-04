<!DOCTYPE html>
<html>
<head>
    <title>Split PDF</title>
</head>
<body>
    <form action="/split-pdf" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="pdf" accept="application/pdf" required>
        <input type="text" name="password" placeholder="pdf-user-password">
        <button type="submit">Split PDF</button>
    </form>
</body>
</html>
