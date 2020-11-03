Drupal.behaviors.PharmaTheme = {
  attach: function(context, settings) {
    var $ = jQuery.noConflict();
    $(".az-search-button").click(function () {
      console.log("aa")
    })

    $(".az-product_body_icons").click(function(){
     
    })
    // $(".az-product_selectbox_conth4").text("免責聲明：");
    // $(".az-product_selectbox_contp1").text("藥品咨詢只能適用於香港或澳門服用此藥物之人仕。");
    // $(".az-product_selectbox_contp2").text("如同意繼續瀏覽，你已聲明你正服用此藥物，並且了解所提供的質詢只作參考用途。如對以上藥物有任何疑問，請向您的醫生或藥劑師查詢。");
    // $(".az-product_selectbox_contp3").text("請確認您是否服用此藥物。");
    // $(".az-product_selectbox_btnyes").html("是");
    // $(".az-product_selectbox_btnno").html("否");

    
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


