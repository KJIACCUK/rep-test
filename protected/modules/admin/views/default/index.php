<?php
    /* @var $this DefaultController */
    /* @var $cs CClientScript */

    $this->pageTitle = Yii::t('application', 'Главная');
    $cs = Yii::app()->clientScript;
    
    $cs->registerScriptFile('/js/moment.min.js');

    $cs->registerScript('dashboard', "
        function bytesToSize(bytes) {
            if(bytes == 0) return '0 Byte';
            var k = 1024;
            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
        }
        
        function padLeft(nr, n, str){
            return Array(n-String(nr).length+1).join(str||'0')+nr;
        }
        
        function integerDivision(x, y){
            return x/y>>0
        }
         
        function setChatServer(online, uptime, memory, socketsCount, nodeVersion, v8Version)
        {
            if(online)
            {
                $('.chat_server_status').html('".TbHtml::labelTb(Yii::t('application', 'онлайн'), array('color' => TbHtml::LABEL_COLOR_SUCCESS))."');
            }
            else
            {
                $('.chat_server_status').html('".TbHtml::labelTb(Yii::t('application', 'оффлайн'), array('color' => TbHtml::LABEL_COLOR_IMPORTANT))."');
            }
            
            if(typeof uptime !== 'undefined')
            {
                var oneDaySeconds = 60 * 60 * 24;
                var days = integerDivision(uptime, oneDaySeconds);

                var oneHourSeconds = 60 * 60;
                var hours = uptime - (days * oneDaySeconds);
                hours = integerDivision(hours, oneHourSeconds);
                
                var oneMinuteSeconds = 60;
                var minutes = uptime - (days * oneDaySeconds + hours * oneHourSeconds);
                minutes = integerDivision(minutes, oneMinuteSeconds);
                
                var uptimeString = '".TbHtml::labelTb('{days}', array('color' => TbHtml::LABEL_COLOR_INVERSE)).' '.TbHtml::labelTb('{hours}', array('color' => TbHtml::LABEL_COLOR_INFO)).':'.TbHtml::labelTb('{minutes}', array('color' => TbHtml::LABEL_COLOR_INFO))."';

                uptimeString = uptimeString.replace('{days}', days);
                uptimeString = uptimeString.replace('{hours}', padLeft(hours, 2));
                uptimeString = uptimeString.replace('{minutes}', padLeft(minutes, 2));

                $('.chat_server_uptime').html(uptimeString);
            }
            else
            {
                $('.chat_server_uptime').text('0');
            }
            
            if(typeof memory !== 'undefined')
            {
                $('.chat_server_memory').text(bytesToSize(memory));
            }
            else
            {
                $('.chat_server_memory').text('0');
            }
            
            if(typeof socketsCount !== 'undefined')
            {
                $('.chat_server_sockets_count').text(socketsCount);
            }
            else
            {
                $('.chat_server_sockets_count').text('0');
            }
            
            if(typeof nodeVersion !== 'undefined')
            {
                $('.chat_server_node_version').text(nodeVersion);
            }
            else
            {
                $('.chat_server_node_version').text('');
            }
            
            if(typeof v8Version !== 'undefined')
            {
                $('.chat_server_v8_version').text(v8Version);
            }
            else
            {
                $('.chat_server_v8_version').text('');
            }
        }
        
        window.updateChatServerSuccessCallback = function(data) {
            if(typeof(data.online) != 'undefined' && data.online)
            {
                setChatServer(true, data.uptime, data.memory, data.socketsCount, data.nodeVersion, data.v8Version);
            }
            else
            {
                setChatServer(false);
            } 
        };
        
        function updateChatServer(){
            $.ajax({
                crossDomain: true,
                type: 'GET',
                url: '".Yii::app()->params['nodeServerUrl']."stats',
                data: {'secret': '".Yii::app()->params['nodeServerSecret']."'},
                dataType: 'jsonp',
                jsonpCallback: 'updateChatServerSuccessCallback',
            });
        }
        
        updateChatServer();
         
        setInterval(function(){
            updateChatServer();
        }, 60000);
    ");
?>
<h1><?php print Yii::t('application', 'Главная'); ?></h1>
<div class="row">
    <div class="span6">
        <h3><?php print Yii::t('application', 'Общая статистика'); ?></h3>
        <table class="table table-bordered" style="width: 100%;">
            <tr>
                <td><code><?php print Yii::t('application', 'Пользователей'); ?></code></td>
                <td><?php print User::model()->count(); ?></td>
            </tr>
            <tr>
                <td><code><?php print Yii::t('application', 'Онлайн'); ?></code></td>
                <td><?php print UserOnline::model()->countByAttributes(array('isOnline' => 1)); ?></td>
            </tr>
            <tr>
                <td><code><?php print Yii::t('application', 'Глобальных мероприятий'); ?></code></td>
                <td><?php print Event::model()->countByAttributes(array('isGlobal' => 1)); ?></td>
            </tr>
            <tr>
                <td><code><?php print Yii::t('application', 'Мероприятий пользователей'); ?></code></td>
                <td><?php print Event::model()->countByAttributes(array('isGlobal' => 0)); ?></td>
            </tr>
            <tr>
                <td><code><?php print Yii::t('application', 'Пройденных маркетинговых исследований'); ?></code></td>
                <td><?php print MarketingResearchUserAnswer::model()->count(); ?></td>
            </tr>
            <tr>
                <td><code><?php print Yii::t('application', 'Заказов в бонусном магазине'); ?></code></td>
                <td><?php print ProductPurchase::model()->count(); ?></td>
            </tr>
        </table>
    </div>
    <div class="span6">
        <h3><?php print Yii::t('application', 'Чат-сервер'); ?></h3>
        <table class="table table-bordered" style="width: 100%;">
            <tr>
                <td><code><?php print Yii::t('application', 'Статус'); ?></code></td>
                <td class="chat_server_status"><?php print TbHtml::labelTb(Yii::t('application', 'оффлайн'), array('color' => TbHtml::LABEL_COLOR_IMPORTANT)); ?></td>
            </tr>
            <tr>
                <td><code><?php print Yii::t('application', 'Время онлайн (дней часов:минут)'); ?></code></td>
                <td class="chat_server_uptime">0</td>
            </tr>
            <tr>
                <td><code><?php print Yii::t('application', 'Память'); ?></code></td>
                <td class="chat_server_memory">0</td>
            </tr>
            <tr>
                <td><code><?php print Yii::t('application', 'Количество подключений'); ?></code></td>
                <td class="chat_server_sockets_count">0</td>
            </tr>
            <tr>
                <td><code><?php print Yii::t('application', 'Версия node.js'); ?></code></td>
                <td class="chat_server_node_version">0</td>
            </tr>
            <tr>
                <td><code><?php print Yii::t('application', 'Версия V8'); ?></code></td>
                <td class="chat_server_v8_version"></td>
            </tr>
        </table>
    </div>
</div>