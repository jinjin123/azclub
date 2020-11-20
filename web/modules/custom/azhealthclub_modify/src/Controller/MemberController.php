<?php

namespace Drupal\azhealthclub_modify\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;
use http\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class MemberController extends ControllerBase {

  public function index() {
    $member = \Drupal::currentUser();
    $variables['memberId'] = $member->id();
    return [
      '#theme' => 'azhealthclub_welcome',
      '#type' => 'markup',
      '#variables' => $variables
    ];
  }

  public function dashboard() {
    $member = \Drupal::currentUser();
    $roles = $member->getRoles();
    if(!in_array('member', $roles)) {
      // todo: not has member role
    }
    $variables['memberId'] = $member->id();
    $storage = \Drupal::entityTypeManager()->getStorage('profile');
    $profile = $storage->loadByUser($member, 'member');
    if ($profile) {
      if ($profile->get('field_gender') == 'female') {
        $variables['genderName'] = '女士';
      }
      else {
        $variables['genderName'] = '先生';
      }
      //$variables['name'] = $member->getDisplayName();
      //$profile->get('field_en_name')->getValue();
      $variables['name'] = $profile->get('field_zh_name')->getValue()[0]['value'];

      return [
        '#theme' => 'azhealthclub_dashboard',
        '#type' => 'markup',
        '#variables' => $variables
      ];
    }
    else {
      // todo: not has member profile
    }

  }

  public function access(AccountInterface $account) {
    if ($account->isAnonymous()) {
      // redirect page
      //$response = new RedirectResponse('/home');
      //$response->send();

      // just forbidden it
      return AccessResult::forbidden();
    }
    else {
      return AccessResult::allowed();
    }
  }

  public function relateclinal()
  {
    $fie = 'field_relate_clinal';
//      'uid' => 'field_user_id',
//      'mail' => 'field_user_mail',
//      'relnode' => 'field_relate_clinal',
    try {
      $uid = \Drupal::currentUser()->id();
//      $mail = \Drupal::request()->request->get('mail');
//      $relnode = \Drupal::request()->request->get('relnode');
      $params = array();
      $content = \Drupal::request()->getContent();
      if (!empty($content)) {
        $params = json_decode($content, TRUE);
//        $mail = $params["mail"];
        $relnode  =$params["relnode"];
      }

      $existUser = User::load($uid);
      $values = array(
//        "uid" => $uid,
//        "mail" => $mail,
        "relnode" => $relnode,
      );
      $vv = array();
      if (isset($values['relnode']) ) {
          if(isset($existUser->$fie->value)){
            foreach(explode(",",$existUser->$fie->value) as $value) {
              array_push($vv,$values['relnode'],$value);
            }
          }else {
            array_push($vv,$values['relnode']);
          }
          $existUser->$fie->value =  implode(",", array_unique($vv));
        }
//      array_walk($map, function ($value, $key) use ( $existUser, $values,$vv) {
//        if (isset($values[$key]) ) {
//          if(isset($existUser->$value->value)){
//            foreach(explode(",",$existUser->$value->value) as $value) {
//              array_push($vv,$values[$key],$value);
//            }
//          }else {
//            array_push($vv,$values[$key]);
//          }
//          $existUser->$value->value =  implode(",", $vv);
//        }
//      });
      $existUser->save();
      return new Response("ok");
//      return new JsonResponse($params);
    }catch (Exception $e){
      return new Response("faild",403);
    }
  }

}
