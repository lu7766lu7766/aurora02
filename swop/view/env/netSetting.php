<?php
//echo "<pre>";print_r($this);
$this->partialView($top_view_path);
?>
    <h3 id="title"><?php echo $this->menu->currentName?></h3>

<table  width="800" class="table table-v">
    <tbody>
    <tr>
        <td>IP 位址 [WAN]:</td>
        <td>
            <input type="text" name="ipaddr" size="20" value="210.242.143.236">
        </td>
    </tr>
    <tr>
        <td>預設閘道 [WAN]:</td>
        <td>
            <input type="text" name="gateway" size="20" value="210.242.143.225">
        </td>
    </tr>
    <tr>
        <td>子網路遮罩 [WAN]:</td>
        <td>
            <input type="text" name="netmask" size="20" value="255.255.255.224">
        </td>
    </tr>
    <tr>
        <td>IP 位址 [LAN]:</td>
        <td>
            <input type="text" name="lipaddr" size="20" value="10.10.10.1">
        </td>
    </tr>
    <tr>
        <td>子網路遮罩 [LAN]:</td>
        <td>
            <input type="text" name="lnetmask" size="20" value="255.255.255.0">
        </td>
    </tr>
    <tr>
        <td>DNS 伺服器:</td>
        <td>
            <input type="text" name="nameserver1" size="20" value="168.95.1.1">
        </td>
    </tr>
    <tr>
        <td>號碼資料庫 IP 位址:</td>
        <td>
            <input type="text" name="dataserver" size="20" value="192.168.1.1">
        </td>
    </tr>
    <tr>
        <td>點撥來源 IP 位址 1:<br>
            點撥來源 IP 位址 2:<br>
            點撥來源 IP 位址 3:<br>
            點撥來源 IP 位址 4:<br>點撥來源 IP 位址 5:<br>
            點撥執行 URL:<br>
        </td>
        <td>
            <input type="text" name="callserver" size="20" value="127.0.0.1"><br>
            <input type="text" name="callserver1" size="20" value="127.0.0.1"><br>
            <input type="text" name="callserver2" size="20" value="127.0.0.1"><br>
            <input type="text" name="callserver3" size="20" value="127.0.0.1"><br>
            <input type="text" name="callserver4" size="20" value="127.0.0.1"><br>
            http://210.242.143.236:8888/dial.crm?caller=XXX&amp;callee=YYY
        </td>
    </tr>
    <tr>
        <td>校時伺服器:</td>
        <td>
            <input type="text" name="ntpserver" size="20" maxlength="45" value="pool.ntp.org">
        </td>
    </tr>
    <tr>
        <td>時區:</td>
        <td>
            <select name="timezone">
                <option value="America/Anchorage">America/Anchorage</option>
                <option value="America/Argentina/Buenos_Aires">America/Argentina/Buenos_Aires</option>
                <option value="America/Chicago">America/Chicago</option>
                <option value="America/Denver">America/Denver</option>
                <option value="America/Guatemala">America/Guatemala</option>
                <option value="America/Halifax">America/Halifax</option>
                <option value="America/Los_Angeles">America/Los_Angeles</option>
                <option value="America/New_York">America/New_York</option>
                <option value="America/Phoenix">America/Phoenix</option>
                <option value="America/Sao_Paulo">America/Sao_Paulo</option>
                <option value="Asia/Aden">Asia/Aden</option>
                <option value="Asia/Almaty">Asia/Almaty</option>
                <option value="Asia/Amman">Asia/Amman</option>
                <option value="Asia/Anadyr">Asia/Anadyr</option>
                <option value="Asia/Aqtobe">Asia/Aqtobe</option>
                <option value="Asia/Ashgabat">Asia/Ashgabat</option>
                <option value="Asia/Baghdad">Asia/Baghdad</option>
                <option value="Asia/Bahrain">Asia/Bahrain</option>
                <option value="Asia/Baku">Asia/Baku</option>
                <option value="Asia/Bangkok">Asia/Bangkok</option>
                <option value="Asia/Beirut">Asia/Beirut</option>
                <option value="Asia/Bishkek">Asia/Bishkek</option>
                <option value="Asia/Calcutta">Asia/Calcutta</option>
                <option value="Asia/Chongqing">Asia/Chongqing</option>
                <option value="Asia/Colombo">Asia/Colombo</option>
                <option value="Asia/Damascus">Asia/Damascus</option>
                <option value="Asia/Dhaka">Asia/Dhaka</option>
                <option value="Asia/Dili">Asia/Dili</option>
                <option value="Asia/Dubai">Asia/Dubai</option>
                <option value="Asia/Dushanbe">Asia/Dushanbe</option>
                <option value="Asia/Gaza">Asia/Gaza</option>
                <option value="Asia/Hong_Kong">Asia/Hong_Kong</option>
                <option value="Asia/Jakarta">Asia/Jakarta</option>
                <option value="Asia/Jayapura">Asia/Jayapura</option>
                <option value="Asia/Jerusalem">Asia/Jerusalem</option>
                <option value="Asia/Kabul">Asia/Kabul</option>
                <option value="Asia/Karachi">Asia/Karachi</option>
                <option value="Asia/Kuwait">Asia/Kuwait</option>
                <option value="Asia/Muscat">Asia/Muscat</option>
                <option value="Asia/Nicosia">Asia/Nicosia</option>
                <option value="Asia/Novosibirsk">Asia/Novosibirsk</option>
                <option value="Asia/Omsk">Asia/Omsk</option>
                <option value="Asia/Oral">Asia/Oral</option>
                <option value="Asia/Phnom_Penh">Asia/Phnom_Penh</option>
                <option value="Asia/Pyongyang">Asia/Pyongyang</option>
                <option value="Asia/Qatar">Asia/Qatar</option>
                <option value="Asia/Qyzylorda">Asia/Qyzylorda</option>
                <option value="Asia/Riyadh">Asia/Riyadh</option>
                <option value="Asia/Samarkand">Asia/Samarkand</option>
                <option value="Asia/Seoul">Asia/Seoul</option>
                <option value="Asia/Shanghai">Asia/Shanghai</option>
                <option value="Asia/Singapore">Asia/Singapore</option>
                <option value="Asia/Taipei" selected="selected">Asia/Taipei</option>
                <option value="Asia/Tashkent">Asia/Tashkent</option>
                <option value="Asia/Tbilisi">Asia/Tbilisi</option>
                <option value="Asia/Tehran">Asia/Tehran</option>
                <option value="Asia/Thimphu">Asia/Thimphu</option>
                <option value="Asia/Tokyo">Asia/Tokyo</option>
                <option value="Asia/Ulaanbaatar">Asia/Ulaanbaatar</option>
                <option value="Asia/Urumqi">Asia/Urumqi</option>
                <option value="Asia/Vientiane">Asia/Vientiane</option>
                <option value="Asia/Yakutsk">Asia/Yakutsk</option>
                <option value="Asia/Yekaterinburg">Asia/Yekaterinburg</option>
                <option value="Asia/Yerevan">Asia/Yerevan</option>
                <option value="Atlantic/Bermuda">Atlantic/Bermuda</option>
                <option value="Australia/Adelaide">Australia/Adelaide</option>
                <option value="Australia/Brisbane">Australia/Brisbane</option>
                <option value="Australia/Darwin">Australia/Darwin</option>
                <option value="Australia/Hobart">Australia/Hobart</option>
                <option value="Australia/Melbourne">Australia/Melbourne</option>
                <option value="Australia/Perth">Australia/Perth</option>
                <option value="Europe/Amsterdam">Europe/Amsterdam</option>
                <option value="Europe/Athens">Europe/Athens</option>
                <option value="Europe/Berlin">Europe/Berlin</option>
                <option value="Europe/Bratislava">Europe/Bratislava</option>
                <option value="Europe/Brussels">Europe/Brussels</option>
                <option value="Europe/Budapest">Europe/Budapest</option>
                <option value="Europe/Copenhagen">Europe/Copenhagen</option>
                <option value="Europe/Dublin">Europe/Dublin</option>
                <option value="Europe/Helsinki">Europe/Helsinki</option>
                <option value="Europe/Kiev">Europe/Kiev</option>
                <option value="Europe/Lisbon">Europe/Lisbon</option>
                <option value="Europe/London">Europe/London</option>
                <option value="Europe/Madrid">Europe/Madrid</option>
                <option value="Europe/Moscow">Europe/Moscow</option>
                <option value="Europe/Oslo">Europe/Oslo</option>
                <option value="Europe/Paris">Europe/Paris</option>
                <option value="Europe/Prague">Europe/Prague</option>
                <option value="Europe/Rome">Europe/Rome</option>
                <option value="Europe/Stockholm">Europe/Stockholm</option>
                <option value="Europe/Warsaw">Europe/Warsaw</option>
                <option value="Europe/Zurich">Europe/Zurich</option>
                <option value="Pacific/Auckland">Pacific/Auckland</option>
                <option value="Pacific/Honolulu">Pacific/Honolulu</option>

            </select>
        </td>
    </tr>
    <tr>
        <td>語音編碼:</td>
        <td>
            <input type="radio" name="codec" value="g729" checked=""> G.729
            <input type="radio" name="codec" value="g723"> G.723
            <input type="radio" name="codec" value="g711"> G.711
        </td>
    </tr>
    <tr>
        <td>SIP Port:</td>
        <td>
            <input type="text" name="bindport" size="20" maxlength="5" value="5060">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input type="submit" value="修改">
        </td>
    </tr>
    </tbody>
</table>

<?php
$this->partialView($bottom_view_path);
?>