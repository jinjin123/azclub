<?php

namespace Drupal\azhealthclub_modify\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;
use http\Exception;
use Drupal\file\Entity\File;
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
      return [
        '#theme' => 'azhealthclub_dashboard',
        '#type' => 'markup',
        '#variables' => $variables
      ];
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

  public function customclinal($user)
  {
    $existUser = User::load($user);
    $database = \Drupal::database();
    $fie = 'field_relate_clinal';
    $fie2 = 'field_clinical_logo_info';
    $fie3 = 'body';
    $fie4="title";
    $Nconnt = array();
    foreach(explode(",",$existUser->$fie->value) as $value) {
      $query = $database->select('path_alias', 'n');
      $npath = $query->condition('n.alias', $value, '=')
        ->fields('n', ['path','alias'])
        ->execute()
        ->fetchAll();
      foreach($npath as $vv){
        $node = \Drupal\node\Entity\Node::load(SUBSTR($vv->path,6));
        $query = $database->select('paragraph__field_az_clinical_page_image', 'n');
        $pimg = $query->condition('n.entity_id', $node->field_clinical_logo_info[0]->target_id, '=')
          ->fields('n', ['field_az_clinical_page_image_target_id'])
          ->execute()
          ->fetchAll();

        array_push($Nconnt,array("short_d"=>$node->body[0]->summary,"target"=>$vv->alias,"title"=>$node->$fie4[0]->value,"time"=>date("d/m/Y",$node->created[0]->value),"img"=>file_create_url(File::load($pimg[0]->field_az_clinical_page_image_target_id)->getFileUri())));
      }
    }

    $variables["memclinal"] = $Nconnt;
    return [
      '#theme' => 'azhealthclub_memberclinal',
      '#variables' => $variables
    ];
  }

}

