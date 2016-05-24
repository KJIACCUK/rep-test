<?php

    class m140627_131809_cities_list extends CDbMigration
    {

        public function up()
        {
            $this->execute("INSERT INTO `city` VALUES "
                    . "('1', 'Браслав', '55.643261', '27.034044', '55.618893', '26.990845', '55.655060', '27.096827'), "
                    . "('2', 'Брест', '52.089512', '23.712021', '52.023605', '23.566170', '52.153095', '23.854521'), "
                    . "('3', 'Дзержинск', '53.681679', '27.135473', '53.669357', '27.089792', '53.707703', '27.179068'), "
                    . "('4', 'Гомель', '52.424160', '31.014273', '52.344578', '30.832048', '52.551010', '31.089741'), "
                    . "('5', 'Городок', '55.463070', '29.984255', '55.436825', '29.950686', '55.485703', '30.025866'), "
                    . "('6', 'Горки', '54.289539', '30.981079', '54.237125', '30.958719', '54.305450', '31.036657'), "
                    . "('7', 'Гродно', '53.678123', '23.829807', '53.608585', '23.720041', '53.759872', '23.990156'), "
                    . "('8', 'Иваново', '52.146553', '25.534451', '52.119846', '25.488016', '52.163822', '25.563438'), "
                    . "('9', 'Клецк', '53.066696', '26.634554', '53.054756', '26.615698', '53.084431', '26.657839'), "
                    . "('10', 'Кобрин', '52.208927', '24.353910', '52.179260', '24.305277', '52.249626', '24.400417'), "
                    . "('11', 'Лида', '53.893242', '25.302847', '53.854713', '25.219250', '53.948288', '25.343235'), "
                    . "('12', 'Любань', '52.792068', '27.998411', '52.786003', '27.980366', '52.808010', '28.015490'), "
                    . "('13', 'Малорита', '51.789513', '24.078865', '51.774776', '24.048250', '51.811718', '24.118076'), "
                    . "('14', 'Мосты', '53.421452', '24.560534', '53.394650', '24.505997', '53.438202', '24.587770'), "
                    . "('15', 'Мозырь', '52.052425', '29.244780', '52.019592', '29.178198', '52.097786', '29.312782'), "
                    . "('16', 'Минск', '53.906078', '27.554914', '53.793880', '27.374416', '53.971588', '28.063091'), "
                    . "('17', 'Новогрудок', '53.597786', '25.825001', '53.574989', '25.780794', '53.615898', '25.854412'), "
                    . "('18', 'Орша', '54.509693', '30.423649', '54.469212', '30.335819', '54.556316', '30.459400'), "
                    . "('19', 'Поставы', '55.112915', '26.840250', '55.092617', '26.782640', '55.137333', '26.891167'), "
                    . "('20', 'Пинск', '52.111645', '26.101467', '52.095322', '26.007610', '52.167896', '26.143938'), "
                    . "('21', 'Солигорск', '52.792107', '27.543488', '52.773403', '27.494017', '52.803444', '27.556433'), "
                    . "('22', 'Сенно', '54.813091', '29.706289', '54.792110', '29.666521', '54.835201', '29.732098'), "
                    . "('23', 'Слоним', '53.093647', '25.320122', '53.049805', '25.260878', '53.118866', '25.410204'), "
                    . "('24', 'Столин', '51.890461', '26.846170', '51.873627', '26.814926', '51.900169', '26.867649'), "
                    . "('25', 'Волковыск', '53.162964', '24.463972', '53.111073', '24.375292', '53.173851', '24.493841'), "
                    . "('26', 'Жабинка', '52.195461', '24.015327', '52.180206', '23.959389', '52.214424', '24.056229'), "
                    . "('27', 'Жлобин', '52.892841', '30.038530', '52.868313', '29.985863', '52.916702', '30.107918')");
        }

        public function down()
        {
            $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
            $this->truncateTable('city');
            $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        }

    }
    