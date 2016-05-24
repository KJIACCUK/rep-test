<?php

    class ImportCommand extends CConsoleCommand
    {

        public function actionCities($path)
        {
            print 'Import Belarus cities from file '.$path.'.'."\n";

            if(!file_exists($path))
            {
                print 'Invalid file path';
                die();
            }

            set_time_limit(0);
            $totalCount = 0;
            $importedCount = 0;
            $errorsCount = 0;
            $citiesTranslation = array(
                'haradok' => 'городок',
                'horki' => 'горки',
                'hrodna' => 'гродно',
                'ivanava' => 'иваново',
                'kleck' => 'клецк',
                'kobryn' => 'кобрин',
                'malaryta' => 'маларита',
                'masty' => 'мосты',
                'mazyr' => 'мозырь',
                'navahrudak' => 'новогрудок',
                'pastavy' => 'поставы',
                'salihorsk' => 'солигорск',
                'vawkavysk' => 'волковыск',
                'zabinka' => 'жабинка',
                'zlobin' => 'жлобин',
            );

            $file = fopen($path, "r");
            fgetcsv($file); // headers
            while(!feof($file))
            {
                $row = fgetcsv($file);
                if($row[0] != 'by')
                {
                    continue;
                }

                if((int)$row[4] < 5000)
                {
                    continue;
                }

                print '   Importing city "'.$row[1].'"...';

                if(array_key_exists($row[1], $citiesTranslation))
                {
                    $row[1] = $citiesTranslation[$row[1]];
                }

                if(YandexMapsHelper::getCityByName($row[1]))
                {
                    print 'done.'."\n";
                    $importedCount++;
                }
                else
                {
                    print 'error.'."\n";
                    $errorsCount++;
                }

                $totalCount++;
                sleep(1);
            }

            fclose($file);

            print 'Done. Total records - '.$totalCount.'. Imported '.$importedCount.' cities. '.$errorsCount.' errors.'."\n";
        }

        public function actionBluestone_users($path)
        {
            print 'Import Bluestone users from file '.$path.'.'."\n";

            if(!file_exists($path))
            {
                print 'Invalid file path'."\n";
                die();
            }

            set_time_limit(0);
            $totalCount = 0;
            $importedCount = 0;
            $errorsCount = 0;
            $db = Yii::app()->db;

            if(($file = fopen($path, "r")) !== false)
            {
                while(($data = fgetcsv($file, 1000, ";")) !== false)
                {
                    if($totalCount == 0)
                    {
                        $totalCount++;
                        continue;
                    }
                    if(count($data) == 2)
                    {
                        if(($user = User::model()->findByAttributes(array('login' => $data[0]))))
                        {
                            /* @var $user User */
                            // $user->setScenario('import_update');
                            // $user->login = $data[0];
                            // $user->password = $data[1];
                            // $user->name = $data[0];

                            // if($user->save())
                            // {
                            //     $importedCount++;
                            // }
                            // else
                            // {
                            //     print 'record '.($totalCount + 1)."\n";
                            //     var_dump($user->getErrors());
                            //     $errorsCount++;
                            // }
                        }
                        else
                        {
                            $transaction = $db->beginTransaction();

                            if(($account = UserHelper::createAccount()))
                            {
                                
                                $user = new User('import');
                                $user->accountId = $account->accountId;
                                $user->login = $data[0];
                                $user->name = $data[0];
                                $user->password = $data[1];
                                $user->isVerified = 1;

                                if($user->save())
                                {
                                    $transaction->commit();
                                    $importedCount++;
                                }
                                else
                                {
                                    print 'record '.($totalCount + 1)."\n";
                                    var_dump($user->getErrors());
                                    $errorsCount++;
                                    $transaction->rollback();
                                }
                            }
                            else
                            {
                                print 'record '.($totalCount + 1)."\n";
                                var_dump($account->getErrors());
                                $errorsCount++;
                                $transaction->rollback();
                            }
                        }
                    }
                    else
                    {
                        $errorsCount++;
                    }
                    $totalCount++;
                }
                fclose($file);
            }

            print 'Done. Total records - '.$totalCount.'. Imported '.$importedCount.' users. '.$errorsCount.' errors.'."\n";
        }

    }
    