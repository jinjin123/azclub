Drupal.behaviors.PharmaTheme = {
  attach: function(context, settings) {
    var $ = jQuery.noConflict();
    $(".az-search-button").click(function () {
      var az_clinic_one_value=$("#az_clinic_select_type option:selected")[0].value;
      var az_clinic_two_value=$("#az_clinic_select_age option:selected")[0].textContent;
      var az_clinic_tqq_value=$("#az_clinic_select_sex option:selected")[0].value;
      if (az_clinic_one_value=="腫瘤類別") {
        az_clinic_one_value=""
      }
      if (az_clinic_two_value=="年齡") {
        az_clinic_two_value=""
      }
      if (az_clinic_tqq_value=="性別") {
        az_clinic_tqq_value=""
      }
      var redirct = '/Clinical/search-result?';
      location.href=redirct+"field_az_conditions_term_target_id="+az_clinic_one_value+"&"+"field_az_se_value="+az_clinic_tqq_value+"&"+"field_az_clinical_age_value="+az_clinic_two_value;
      // console.log(redirct+"field_az_conditions_term_target_id="+az_clinic_two_value+"&"+"field_az_se_value="+az_clinic_two_value+"&"+"field_az_clinical_age_value="+az_clinic_tqq_value)
    })

    $(".az-product_body_icons").on('click',function(e){
      e.stopPropagation();
      var target = $(this);
      var title = target.parent().children()[0].textContent.replace(/^\s+|\s+$/g,"");
      var cont = target.parent().children()[1].textContent.replace(/^\s+|\s+$/g,"");
      var imgUrl= target.parent().parent().children()[0].innerHTML.replace(/^\s+|\s+$/g,"");
      $(".az-product_selectbox").css("display","block");
      // console.log(title);

      // var product_des
  /*       //img
        var az_product_repeimgparent=$(this).parent().siblings(".az-product_img");
        var az_product_repeimg=az_product_repeimgparent.find("img").attr('src');
        //title
        var az_product_titleparent=$(this).siblings(".az-product_body_title");
        var az_product_title=az_product_titleparent.find("a").html();
        //content
        var az_product_contparent= $(this).siblings(".az-product_body_desc");
        var az_product_cont=az_product_contparent.find("p").html(); */
        $(".az-product_selectbox_btnyes").click(function(){
      $(".az-product_selectbox").css("display","none");
      $(".az-product_duplicate").css("display","block");
      $(".az-product_duplicate_img").html(imgUrl)
     $(".az-product_duplicate_body").children()[0].textContent=title;
     $(".az-product_duplicate_body").children()[2].textContent=cont;
    })
    //NO
    $(".az-product_selectbox_btnno").click(function(){
      $(".az-product_selectbox").css("display","none");
    })
    //close
    $(".az-product_content_close").click(function(){
      $(".az-product_duplicate").css("display","none");
    })
    })

    var input =document.createElement("input");
    input.setAttribute("type","search");
    input.setAttribute("name","clincal");
    input.setAttribute("placeholder","Search");
    $(".az-clincal-search_button").html(input);

    // $(".az-product_selectbox_conth4").text("免責聲明：");
    // $(".az-product_selectbox_contp1").text("藥品咨詢只能適用於香港或澳門服用此藥物之人仕。");
    // $(".az-product_selectbox_contp2").text("如同意繼續瀏覽，你已聲明你正服用此藥物，並且了解所提供的質詢只作參考用途。如對以上藥物有任何疑問，請向您的醫生或藥劑師查詢。");
    // $(".az-product_selectbox_contp3").text("請確認您是否服用此藥物。");
    // $(".az-product_selectbox_btnyes").html("是");
    // $(".az-product_selectbox_btnno").html("否");

    // health tips bg
   // $(".az-healthtips_head").parent().parent().css("background-color","oranage");
  }
};
  //http://localhost:30000/Clinical/search-result?field_az_conditions_term_target_id=10&field_az_se_value=1&field_az_clinical_age_value=1
  // $(".az-search-button").click(function(){
  //   var clinic_s_type=$("#az_clinic_select_type").val();
  //   alert(clinic_s_type.val());  console.log();
  // })
  // console.log("fdsfsf")
  // $(".body-page").on('click',function (){
  //   console.log("fdsfsf")
  // })


