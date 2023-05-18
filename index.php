<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>查询Mac地址厂商</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 50px;
        }

        p {
            text-align: center;
            margin-top: 30px;
        }

        textarea {
            display: block;
            margin: auto;
            width: 80%;
            padding: 10px;
            resize: none;
        }

        button {
            display: block;
            margin: auto;
            margin-top: 20px;
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #0062cc;
        }

        table {
            margin: auto;
            margin-top: 30px;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>查询Mac地址厂商</h1>
    <p>复制粘贴要查询的Mac地址：</p>
    <form method="post" action="">
        <textarea name="macInput" rows="5" placeholder="输入Mac地址"></textarea>
        <button type="submit" name="submit">查询</button>
    </form>
    <?php
    if (isset($_POST['submit'])) {
        $macs = preg_split('/\r\n|\r|\n/', $_POST['macInput']); // 分割多行Mac地址
        ?>
        <table>
            <thead>
                <tr>
                    <th>Mac地址</th>
                    <th>厂商</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($macs as $mac) { 
                    $mac = strtr($mac, 'O', '0'); 
                    $mac = strtr($mac, 'o', '0'); 
                    $mac = strtr($mac, 'i', '1'); 
                    $mac = strtr($mac, 'l', '1'); 
                    $mac = strtr($mac, 'I', '1'); 
                    $mac = strtr($mac, 'L', '1'); 
                    $mac = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $mac)); // 去掉空格等符号并转为大写字母
                    $mac = substr($mac, 0, 12);
                    $url = 'https://api.macvendors.com/' . substr($mac, 0, 6); // 拼接API URL
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);
                    curl_close($ch);
                    ?>
                    <tr>
                        <td><?php echo $mac; ?></td>
                        <td><?php echo $result !== '{"errors":{"detail":"Not Found"}}' ? $result : "未知厂商"; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</body>
</html>
