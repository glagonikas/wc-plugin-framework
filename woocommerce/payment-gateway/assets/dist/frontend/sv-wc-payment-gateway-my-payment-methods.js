parcelRequire=function(e,r,t,n){var i,o="function"==typeof parcelRequire&&parcelRequire,u="function"==typeof require&&require;function f(t,n){if(!r[t]){if(!e[t]){var i="function"==typeof parcelRequire&&parcelRequire;if(!n&&i)return i(t,!0);if(o)return o(t,!0);if(u&&"string"==typeof t)return u(t);var c=new Error("Cannot find module '"+t+"'");throw c.code="MODULE_NOT_FOUND",c}p.resolve=function(r){return e[t][1][r]||r},p.cache={};var l=r[t]=new f.Module(t);e[t][0].call(l.exports,p,l,l.exports,this)}return r[t].exports;function p(e){return f(p.resolve(e))}}f.isParcelRequire=!0,f.Module=function(e){this.id=e,this.bundle=f,this.exports={}},f.modules=e,f.cache=r,f.parent=o,f.register=function(r,t){e[r]=[function(e,r){r.exports=t},{}]};for(var c=0;c<t.length;c++)try{f(t[c])}catch(e){i||(i=e)}if(t.length){var l=f(t[t.length-1]);"object"==typeof exports&&"undefined"!=typeof module?module.exports=l:"function"==typeof define&&define.amd?define(function(){return l}):n&&(this[n]=l)}if(parcelRequire=f,i)throw i;return f}({"nDDW":[function(require,module,exports) {
function e(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function t(e,t){for(var n=0;n<t.length;n++){var i=t[n];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(e,i.key,i)}}function n(e,n,i){return n&&t(e.prototype,n),i&&t(e,i),e}(function(){jQuery(function(t){"use strict";return window.SV_WC_Payment_Methods_Handler_v5_10_8=function(){function i(n){var o=this;e(this,i),this.replace_method_column=this.replace_method_column.bind(this),this.remove_duplicate_default_marks=this.remove_duplicate_default_marks.bind(this),this.edit_method=this.edit_method.bind(this),this.save_method=this.save_method.bind(this),this.cancel_edit=this.cancel_edit.bind(this),this.id=n.id,this.slug=n.slug,this.i18n=n.i18n,this.ajax_url=n.ajax_url,this.ajax_nonce=n.ajax_nonce,this.replace_method_column(),this.remove_duplicate_default_marks(),t(".woocommerce-MyAccount-paymentMethods").on("click",".woocommerce-PaymentMethod--actions .button.edit",function(e){return o.edit_method(e)}),t(".woocommerce-MyAccount-paymentMethods").on("click",".woocommerce-PaymentMethod--actions .button.save",function(e){return o.save_method(e)}),t(".woocommerce-MyAccount-paymentMethods").on("click",".woocommerce-PaymentMethod--actions .cancel-edit",function(e){return o.cancel_edit(e)}),t(".woocommerce-MyAccount-paymentMethods").on("click",".woocommerce-PaymentMethod--actions .button.delete",function(e){if(0!==t(e.currentTarget).parents("tr").find("input[name=plugin-id][value=".concat(o.slug,"]")).length)return t(e.currentTarget).hasClass("disabled")||!confirm(o.i18n.delete_ays)?e.preventDefault():void 0}),t('.button[href*="add-payment-method"]').click(function(e){if(t(this).hasClass("disabled"))return e.preventDefault()})}return n(i,[{key:"replace_method_column",value:function(){var e=this;return t(".woocommerce-MyAccount-paymentMethods").find("tr").each(function(n,i){var o;if(0!==t(i).find("input[name=plugin-id][value=".concat(e.slug,"]")).length)return t(i).find("th.woocommerce-PaymentMethod--title").remove(),(o=t(i).find("td.woocommerce-PaymentMethod--title")).children().length>0&&t(i).find("td.woocommerce-PaymentMethod--method").html(o.html()),t(i).find("td.woocommerce-PaymentMethod--title").remove()})}},{key:"remove_duplicate_default_marks",value:function(){return t(".woocommerce-MyAccount-paymentMethods").find("tr").each(function(e,n){return t(n).find("td.woocommerce-PaymentMethod--default").find("mark.default:not(:first-child)").remove()})}},{key:"edit_method",value:function(e){var n,i;if(e.preventDefault(),0!==(i=(n=t(e.currentTarget)).parents("tr")).find("input[name=plugin-id][value=".concat(this.slug,"]")).length)return i.find("div.view").hide(),i.find("div.edit").show(),i.addClass("editing"),n.text(this.i18n.cancel_button).removeClass("edit").addClass("cancel-edit").removeClass("button"),this.enable_editing_ui()}},{key:"save_method",value:function(e){var n,i,o,a=this;if(e.preventDefault(),n=t(e.currentTarget),0!==(o=n.parents("tr")).find("input[name=plugin-id][value=".concat(this.slug,"]")).length)return this.block_ui(),o.next(".error").remove(),i={action:"wc_".concat(this.id,"_save_payment_method"),nonce:this.ajax_nonce,token_id:o.find("input[name=token-id]").val(),data:o.find("input[name]").serialize()},t.post(this.ajax_url,i).done(function(e){return e.success?(null!=e.data.title&&o.find(".woocommerce-PaymentMethod--method").html(e.data.title),null!=e.data.nonce&&(a.ajax_nonce=e.data.nonce),n.siblings(".cancel-edit").removeClass("cancel-edit").addClass("edit").text(a.i18n.edit_button).addClass("button"),a.disable_editing_ui()):a.display_error(o,e.data)}).fail(function(e,t,n){return a.display_error(o,n)}).always(function(){return a.unblock_ui()})}},{key:"cancel_edit",value:function(e){var n,i;if(e.preventDefault(),0!==(i=(n=t(e.currentTarget)).parents("tr")).find("input[name=plugin-id][value=".concat(this.slug,"]")).length)return i.find("div.view").show(),i.find("div.edit").hide(),i.removeClass("editing"),n.removeClass("cancel-edit").addClass("edit").text(this.i18n.edit_button).addClass("button"),this.disable_editing_ui()}},{key:"enable_editing_ui",value:function(){return t(".woocommerce-MyAccount-paymentMethods").addClass("editing"),t('.button[href*="add-payment-method"]').addClass("disabled")}},{key:"disable_editing_ui",value:function(){return t(".woocommerce-MyAccount-paymentMethods").removeClass("editing"),t('.button[href*="add-payment-method"]').removeClass("disabled")}},{key:"block_ui",value:function(){return t(".woocommerce-MyAccount-paymentMethods").parent("div").block({message:null,overlayCSS:{background:"#fff",opacity:.6}})}},{key:"unblock_ui",value:function(){return t(".woocommerce-MyAccount-paymentMethods").parent("div").unblock()}},{key:"display_error",value:function(e,n){var i,o=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"";return console.error(n),o||(o=this.i18n.save_error),i=t(".woocommerce-MyAccount-paymentMethods thead tr th").length,t('<tr class="error"><td colspan="'+i+'">'+o+"</td></tr>").insertAfter(e).find("td").delay(8e3).slideUp(200)}}]),i}(),t(document.body).trigger("sv_wc_payment_methods_handler_v5_10_8_loaded")})}).call(this);
},{}]},{},["nDDW"], null)
//# sourceMappingURL=../frontend/sv-wc-payment-gateway-my-payment-methods.js.map
