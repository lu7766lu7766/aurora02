<?php
$this->partialView($top_view_path);
?>
    <h3 id="title"><?php echo $this->menu->currentName?></h3>

    <div class="table-responsive">
        <table class="table table-v">
            <tbody>
            <tr>
                <td>*0</td>
                <td>
                    分機可使用此按鍵，暫停接聽自動撥號的來電
                </td>
            </tr>
            <tr>
                <td>*1</td>
                <td>
                    分機可使用此按鍵，啟用接聽自動撥號的來電
                </td>
            </tr>
            <tr>
                <td>*00</td>
                <td>
                    分機可使用此按鍵，暫停來電接通自動播放已錄製好的前言
                </td>
            </tr>
            <tr>
                <td>*01</td>
                <td>
                    分機可使用此按鍵，啟用來電接通自動播放已錄製好的前言
                </td>
            </tr>

            <tr>
                <td>*4 + 分機</td>
                <td>
                    [ 唯聽模式 ] 聽取該分機的通話, [ 無法 ] 與該分機單向通話<br>
                    通話中切換模式, 按 4 唯聽模式, 按 5 單向模式, 按 6 三方模式
                </td>
            </tr>
            <tr>
                <td>*5 + 分機</td>
                <td>
                    [ 單向模式 ] 聽取該分機的通話, [ 可以 ] 與該分機單向通話<br>
                    通話中切換模式, 按 4 唯聽模式, 按 5 單向模式, 按 6 三方模式
                </td>
            </tr>
            <tr>
                <td>*6 + 分機</td>
                <td>
                    [ 三方模式 ] 聽取該分機的通話, [ 可以 ] 與該通話的主被叫三方通話<br>
                    通話中切換模式, 按 4 唯聽模式, 按 5 單向模式, 按 6 三方模式
                </td>
            </tr>

            <tr>
                <td>*71</td>
                <td>
                    錄製分機前言，聽到逼聲後開始錄音，結束請按＃字鍵
                </td>
            </tr>
            <tr>
                <td>*72</td>
                <td>
                    聽取分機前言
                </td>
            </tr>
            <tr>
                <td>*900</td>
                <td>
                    分機使用按鍵，經由密碼驗證，關閉自動撥號
                </td>
            </tr>
            <tr>
                <td>*901</td>
                <td>
                    分機使用按鍵，經由密碼驗證，啟動自動撥號
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    按任意鍵可停止前言的播放
                </td>
            </tr>
            </tbody>
        </table>
    </div>

<?php
$this->partialView($bottom_view_path);
?>