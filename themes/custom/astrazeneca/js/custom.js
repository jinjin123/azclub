Drupal.behaviors.PharmaTheme = {
  attach: function(context, settings) {
    var $ = jQuery.noConflict();
    $(".az-search-button").click(function () {
      var az_clinic_one_value=$("#az_clinic_select_type option:selected")[0].value;
      // var az_clinic_two_value=$("#az_clinic_select_age option:selected")[0].textContent;
      var az_clinic_tqq_value=$("#az_clinic_select_sex option:selected")[0].value;
      if (az_clinic_one_value=="腫瘤類別") {
        az_clinic_one_value=""
      }
      // if (az_clinic_two_value=="年齡") {
      //   az_clinic_two_value=""
      // }
      if (az_clinic_tqq_value=="性別") {
        az_clinic_tqq_value=""
      }
      var redirct = '/Clinical/search-result?';
      // location.href=redirct+"field_az_conditions_term_target_id="+az_clinic_one_value+"&"+"field_az_se_value="+az_clinic_tqq_value+"&"+"field_az_clinical_age_value="+az_clinic_two_value;
      location.href=redirct+"field_az_conditions_term_target_id="+az_clinic_one_value+"&"+"field_az_se_value="+az_clinic_tqq_value;
      // console.log(redirct+"field_az_conditions_term_target_id="+az_clinic_two_value+"&"+"field_az_se_value="+az_clinic_two_value+"&"+"field_az_clinical_age_value="+az_clinic_tqq_value)
    })

    $(".az-product_body_icons").on('click',function(e){
      // console.log($(".ui-widget-header").children()[0].classList)
      var qktitle = $(".ui-widget-header").children()

      for(var qb=0;qb<qktitle.length;qb++){
           if(qktitle[qb].classList.contains("ui-tabs-active")){
              inline_icon = $(qktitle[qb]).children().children()[0].outerHTML;
           }

        // console.log(qktitle[qb].classList)
      }
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
          $(".az-product-icon").html(inline_icon)
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
    var productinput =document.createElement("input");
    productinput.setAttribute("type","search");
    productinput.setAttribute("name","product");
    productinput.setAttribute("placeholder","Search");
    $(".az-product-search_button").html(productinput);

    var clincalinput =document.createElement("input");
    clincalinput.setAttribute("type","search");
    clincalinput.setAttribute("name","clincal");
    clincalinput.setAttribute("placeholder","Search");
    $(".az-clincal-search_button").html(clincalinput);


    $(".az-clinical-bbx-email").on('click',function(e){
      $(".az-clinical-subjest-email").css("display","block");
        $(".az-clinical-subjest_btncancel").click(function(){
          $(".az-clinical-subjest-email").css("display","none");
      })
      $(".az-clinical-subjest_btnemail").click(function (){
         if($(".az-clinical-ckb")[0].checked){
           // console.log("aa")
           var mail = $(".az-clinical-inputemil")[0].value
           var relnode = location.pathname
           var dataser = {"mail":mail,"relnode":relnode}
           $.post("/relateclinal", JSON.stringify(dataser), function (data) {
             if(data=="ok"){
               $(".az-clinical-subjest-email").css("display","none");
             }
           }).fail(function (){
             alert("请登录");
           })
         }else {
           $(".az-clinical-ckb-box").css("border","1px solid red")
         }
      })
    })
    // $(".az-clinical-subjest_btnemail").click(function (){
    //   $(".az-clinical-ckb")[0].checked
    // })

    // product shop search
    var oldtmp=[]
    if(($(".az-off-pp-area").length > 0)){
      var ofselect = $(".az-off-pp-area")
      $("#az-medicine-shoparea").children().remove();
      $("#az-medicine-shoparea").append("<option>選擇地區</option>");
      for(var i=0;i<ofselect.length;i++){
        oldtmp.push(ofselect[i].textContent.trim())
      }
      var newtmp = unique(oldtmp);
      for(var x=0;x<newtmp.length;x++){
          $("#az-medicine-shoparea").append("<option  value='"+newtmp[x] +"'>"+newtmp[x]+"</option>")
      }
    }
    function unique(arr){
      var newArr = [];
      for(var i = 0; i < arr.length; i++){
        if(newArr.indexOf(arr[i]) == -1){
          newArr.push(arr[i])
        }
      }
      return newArr;
    }
    $("#az-medicine-shoparea").change(function(){
       var chooseVal = $("#az-medicine-shoparea option:selected")[0].value
         var newchoose = $(".az-offerdetail")
         for(var xx =0; xx<newchoose.length;xx++){
             if($(".az-offerdetail")[xx].childNodes[4].textContent.trim() != chooseVal){
               $($(".az-offerdetail")[xx]).css("display","none")
             }
         }
        if(chooseVal == "選擇地區"){
          for(var xx =0; xx<newchoose.length;xx++){
              $($(".az-offerdetail")[xx]).css("display","flex")
          }
        }
    })

    $(".az-clincal-search_button").keydown(function(e){
      if(e.keyCode == 13){
        var _keydown = $(".az-clincal-search_button").children()[0].value;
        var _search_result_item = $($(".az-clinical-result-content .view-content-wrap")[0]).children();
        for(var xb=0;xb<_search_result_item.length;xb++){
          if($($(_search_result_item[xb]).children().children().children().children()[1]).children().children()[0].textContent.trim() != _keydown && _keydown !=""){
           $(_search_result_item[xb]).css("display","none")
          }else if(_keydown ==""){
            $(_search_result_item[xb]).css("display","block")
          }
        }
      }
    })

    $(".az-product-search_button").keydown(function(e){
      if(e.keyCode == 13){
        var target = $(this)
        var tmp = target.children()[0].value
        var  _search_result_item = $($($(target).parent().parent().parent().parent().parent().children()[2]).children()[0]).children()
        for(var xb=0;xb<_search_result_item.length;xb++){
            if($($($(_search_result_item[xb]).children()).children().children().children().children()[1]).children()[0].textContent.trim() != tmp && tmp !=""){
              $(_search_result_item[xb]).css("display","none")
            }else {
              $(_search_result_item[xb]).css("display","block")
            }
        }
      }
    })

    $(".az-valicode_freash").click(function(){
       $("#az-contact_valid_code")[0].value = RndNum(5)
      // console.log($("#az-contact_valid_code")[0].value)
    })
    // console.log($(".az-contact-form_body").length)
    if(($(".az-contact-form_body").length > 0)){
      $(".az-valicodeshow").html('<input  id="az-contact_valid_code" disabled  value="">')
      $(".az-valicode_freash").html('<input  type="button" id="az-contact_freshbtn"  value="刷新">')
      $("#az-contact_valid_code")[0].value = RndNum(5)
    }
    function RndNum(n){
      var rnd="";
      for(var i=0;i<n;i++)
        rnd+=Math.floor(Math.random()*10);
      return rnd.split("").join(".");
    }

    //mobile menu
    $(".close").click(function() {
      $("nav").css("display","none")
    });
    $("#menu-bar").click(function() {
      $("nav").css("display","block")
    });

    $('.sidebar-nav > li', context).click(function() {
      if (!$(this).hasClass('parent')) {
        $('.sidebar-nav > li').each(function() {
          $(this).removeClass('active');
        });
        $(this).addClass('active');
      }
    });
    $('.sidebar-nav > li.parent', context).click(function() {
      $('.sidebar-nav > li.parent').find('.sub-sidebar-nav').hide();
      if ($(this).hasClass('show-sub')) {
        $(this).removeClass('show-sub');
      } else {
        $(this).addClass('show-sub ');
      }
      $(this).find('.sub-sidebar-nav').slideToggle();
    });
    // Remove first existant
    $('.sidebar-nav > li.parent', context).each(function() {
      $(this).find('li').first().remove();
    });
    /*sidebar*/
    if($(".az-fk-container").length >0) {
      $(".footer").css("margin-top","0")
      $("#block-zixunyisheng").css("margin","0")
    }

    // health tips bg
   // $(".az-healthtips_head").parent().parent().css("background-color","oranage");

  //top banner
  //   $("body").click(function(){
  //     console.log($(".breadcrumb")[0].innerText)
  //   })
  //   var ttt = $(".breadcrumb")[0].innerText.split(" ")
  //   $(".az-top-banner h1")[0].textContent = ttt[1].replace("-","") + ttt[2].replace("-","/") + "\n" +ttt[3].replace("-","/") + ttt[4].replace("-","/") + ttt[5].replace("-","/")
  //todo bug
    // if($(".az-top-banner").length > 0){
    //   $(".az-top-banner h1")[0].textContent =  $(".breadcrumb")[0].innerText
    // }
  }
};
