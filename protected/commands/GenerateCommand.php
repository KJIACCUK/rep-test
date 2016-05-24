<?php

class GenerateCommand extends CConsoleCommand
{

  public function actionIndex()
  {
    print 'Budutam Data Generator';
  }

  public function actionUsers($count = 100)
  {
      print 'Creating '.$count.' users.'."\n";

      set_time_limit(0);
      $successCount = 0;
      $errorsCount = 0;

      for($i = 0; $i < $count; $i++)
      {
          $httpClient = new EHttpClient('http://'.Yii::app()->params['hostname'].'/api/registration', array(
              'maxredirects' => 0,
              'timeout' => 30));

          $post = array(
              'name' => 'Test '.CommonHelper::randomString(8),
              'email' => time().'_'.$i.'mail'.CommonHelper::randomString(8).'@'.Yii::app()->params['hostname'],
              'birthday' => str_pad(rand(1, 27), 2, '0', STR_PAD_LEFT).'.'.str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT).'.'.rand(1970, 1990),
              'phone' => rand(1000000, 9999999),
              'phoneCode' => 29,
              'messenger' => 'skype',
              'messengerLogin' => CommonHelper::randomString(8),
              'login' => 'user_'.$i.'_'.time(),
              'password' => 111111,
              'image' => '150x150',
              'platform' => 'android'
          );

          print '   '.($i+1).' - Creating user "'.$post['name'].'"...';

          $httpClient->setParameterPost($post);
          $response = $httpClient->request('POST');

          if($response->isSuccessful())
          {
              $data = $response->getBody();
              $data = CJSON::decode($data);
              if($data)
              {
                  if(isset($data['data']))
                  {
                      $successCount++;
                      print 'done.'."\n";
                  }
                  elseif($data['error'])
                  {
                      $errorsCount++;
                      print 'fail. Error '.$data['error']['code'].' - "'.$data['error']['message'].'".';
                      if($data['error']['code'] == 1000)
                      {
                          print CJSON::encode($data['error']['data']);
                      }
                      print "\n";
                  }
                  else
                  {
                      $errorsCount++;
                      print 'fail. Bad response structure.'."\n";
                  }
              }
          }
          else
          {
              $errorsCount++;
              print 'fail. Response does not successed.'."\n";
          }
      }

      print 'Done. Generated '.$successCount.' users. '.$errorsCount.' errors.'."\n";
  }

  public function actionPro_users($un, $count = 100)
  {
    print 'Creating '.$count.' users.'."\n";

    set_time_limit(0);
    $errors_count = 0;

    $filename = Yii::getPathOfAlias('application.content.validation_attachments').DIRECTORY_SEPARATOR.date('Ymd_').$un.'.csv';
    $filename = str_replace('/protected', '', $filename);

    $csv = fopen($filename, 'a+');

    for($i = 0; $i < $count;)
    {
      $user_row = $this->addUser($un);

      if ($user_row)
      {
        fputcsv($csv, $user_row);

        print '   '.($i+1).' - Creatd user "'.$user_row['login'].'"...'."\n";

        $i++;
      }
      else
      {
        $errors_count++;
      }
    }

    fclose($csv);

    print 'Done. Generated '.$i.' users. '.$errors_count.' errors.'."\n";
  }

  private function addUser($un)
  {
    $g_user = array(
      'login' => $un.'_'.CommonHelper::randomString(4),
      'password' => CommonHelper::randomString(8),
    );

    $user = new User('generate_verified');

    $transaction = Yii::app()->db->beginTransaction();

    if (!($account = UserHelper::createAccount()))
    {
      $transaction->rollback();

//      throw new CHttpException(500, Yii::t('application', 'Ошибка сервера. Попробуйте еще раз'));
      return false;
    }

    $user->attributes = $g_user;
    $user->accountId = $account->accountId;

    if ($user->save())
    {
      if (!UserNotificationsHelper::createSettings($user->userId))
      {
        $transaction->rollback();

//        throw new CHttpException(500, Yii::t('application', 'Ошибка сервера. Попробуйте еще раз'));
        return false;
      }

      PointHelper::addPoints(Point::KEY_VERIFICATION, $user->userId);

      $transaction->commit();
    }
    else
    {
      $transaction->rollback();

//      throw new CHttpException(500, Yii::t('application', 'Validation error.'));
      return false;
    }

    return $g_user;
  }
}
    