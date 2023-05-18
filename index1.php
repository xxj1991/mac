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
                    <th>编号</th>
                    <th>Mac地址</th>
                    <th>厂商</th>
                    <th>IP地址</th>
                </tr>
            </thead>
            <tbody>
                <?php
                function IpAddress($str, &$ip) {
                    $ip = '';
                    return preg_replace_callback('/\b(?:[0-9]{1,3}\.){3}[0-9]{1,3}\b/', function ($matches) use (&$ip) {
                        $ip = $matches[0];
                        return '';
                    }, $str);
                }
                $file = fopen("oui.txt", "r"); // 打开oui.txt文件
                $vendors = array();
                $num = 1;
                while (!feof($file)) {
                    $line = fgets($file);
                    if (strpos($line, "(base 16)") !== false) {
                        $vendor = trim(substr($line, strpos($line, "(base 16)") + 9));
                        $vendor = str_replace(" CO.,LTD", "", $vendor);                   
                        $vendor = str_replace(" CO., LTD.", "", $vendor);
                        $vendor = str_replace(" Co.,Ltd.", "", $vendor);
                        $vendor = str_replace(", Inc.", "", $vendor);
                        $vendor = str_replace(" Ltd.", "", $vendor);
                        $vendor = str_replace(" Inc.", "", $vendor);
                        $vendor = str_replace(" Co.,", "", $vendor);
                        $vendor = str_replace(" Digital Technology", "", $vendor);
                        $vendor = str_replace(" Technology", "", $vendor);
                        $mac_prefix = strtoupper(substr($line, 0, 6));
                        $vendors[$mac_prefix] = $vendor;
                    }
                }
                fclose($file); // 关闭oui.txt文件
                foreach ($macs as $mac) { 
                    $ip = "";
                    IpAddress($mac, $ip);
                    $mac = preg_replace('/\b(?:[0-9]{1,3}\.){3}[0-9]{1,3}\b/', '', $mac);
                    $mac = strtr($mac, 'O', '0'); 
                    $mac = strtr($mac, 'o', '0'); 
                    $mac = strtr($mac, 'i', '1'); 
                    $mac = strtr($mac, 'l', '1'); 
                    $mac = strtr($mac, 'I', '1'); 
                    $mac = strtr($mac, 'L', '1'); 
                    $mac = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $mac)); // 去掉空格等符号并转为大写字母
                    $mac = substr($mac, 0, 12);
                    $vendor = isset($vendors[substr($mac, 0, 6)]) ? $vendors[substr($mac, 0, 6)] : "未知厂商";
                    ?>
                    <tr>
                        <td><?php echo $num++; ?></td>
                        <td><?php echo $mac; ?></td>
                        <td><?php echo $vendor; ?></td>
                        <td><?php echo $ip; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</body>
</html>
