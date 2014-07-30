define([],function(){"use strict";var a="#bank-account-form",b={headline:"contact.accounts.title"},c={bankAccountsId:"#bankAccounts",bankAccountAddId:"#bank-account-add",addBankAccountsWrapper:".grid-row",overlayIdTermsOfPayment:"overlayContainerTermsOfPayment",overlayIdTermsOfDelivery:"overlayContainerTermsOfDelivery",overlaySelectorTermsOfPayment:"#overlayContainerTermsOfPayment",overlaySelectorTermsOfDelivery:"#overlayContainerTermsOfDelivery",cgetTermsOfDeliveryURL:"api/termsofdeliveries",cgetTermsOfPaymentURL:"api/termsofpayments",getTermsOfDeliveryURL:"api/termsofdelivery",getTermsOfPaymentURL:"api/termsofpayment"},d={addBankAccountsIcon:['<div class="grid-row">','    <div class="grid-col-12">','       <span id="bank-account-add" class="fa-plus-circle icon bank-account-add clickable pointer m-left-140"></span>',"   </div>","</div>"].join("")};return{view:!0,layout:{sidebar:{width:"fixed",cssClasses:"sidebar-padding-50"}},templates:["/admin/contact/template/account/financials"],initialize:function(){this.options=this.sandbox.util.extend(!0,{},b,this.options),this.saved=!0,this.form="#financials-form",this.termsOfDeliveryInstanceName="terms-of-delivery",this.termsOfPaymentInstanceName="terms-of-payment",this.setHeaderBar(!0),this.render(),this.listenForChange(),this.options.data&&this.options.data.id&&this.initSidebar("/admin/widget-groups/account-detail?account=",this.options.data.id)},initSidebar:function(a,b){this.sandbox.emit("sulu.sidebar.set-widget",a+b)},render:function(){var a=this.options.data;this.html(this.renderTemplate(this.templates[0])),this.initForm(a),this.bindDomEvents(),this.bindCustomEvents()},startTermsOfPaymentOverlay:function(){var a=this.sandbox.dom.createElement("<div/>");this.sandbox.dom.append(this.$el,a),this.sandbox.start([{name:"type-overlay@suluadmin",options:{el:a,overlay:{el:c.overlaySelectorTermsOfPayment,instanceName:this.termsOfPaymentInstanceName,removeOnClose:!0},instanceName:this.termsOfPaymentInstanceName,url:c.cgetTermsOfPaymentURL,data:this.termsOfPaymentData}}])},startTermsOfDeliveryOverlay:function(){var a=this.sandbox.dom.createElement("<div/>");this.sandbox.dom.append(this.$el,a),this.sandbox.start([{name:"type-overlay@suluadmin",options:{el:a,overlay:{el:c.overlaySelectorTermsOfDelivery,instanceName:this.termsOfDeliveryInstanceName,removeOnClose:!0},instanceName:this.termsOfDeliveryInstanceName,url:c.cgetTermsOfDeliveryURL,data:this.termsOfDeliveryData}}])},initTermsSelect:function(a){this.preselectedTermsOfPaymentId=a.termsOfPayment?a.termsOfPayment.id:null,this.termsOfPaymentData=null,this.preselectedTermsOfDeliveryId=a.termsOfDelivery?a.termsOfDelivery.id:null,this.termsOfDeliveryData=null,this.sandbox.util.load(c.cgetTermsOfPaymentURL).then(function(a){var b=a._embedded.termsOfPayments;this.termsOfPaymentData=this.copyArrayOfObjects(b),this.sandbox.util.foreach(b,function(a){a.terms=this.sandbox.translate(a.terms)}.bind(this)),this.addDividerAndActionsForPaymentSelect(b),this.sandbox.start([{name:"select@husky",options:{el:"#termsOfPayment",instanceName:this.termsOfPaymentInstanceName,multipleSelect:!1,defaultLabel:this.sandbox.translate("contact.accounts.termsOfPayment.select"),valueName:"terms",repeatSelect:!1,preSelectedElements:[this.preselectedTermsOfPaymentId],data:b}}])}.bind(this)).fail(function(a,b){this.sandbox.logger.error(a,b)}.bind(this)),this.sandbox.util.load(c.cgetTermsOfDeliveryURL).then(function(a){var b=a._embedded.termsOfDeliveries;this.termsOfDeliveryData=this.copyArrayOfObjects(b),this.sandbox.util.foreach(b,function(a){a.terms=this.sandbox.translate(a.terms)}.bind(this)),this.addDividerAndActionsForDeliverySelect(b),this.sandbox.start([{name:"select@husky",options:{el:"#termsOfDelivery",instanceName:this.termsOfDeliveryInstanceName,multipleSelect:!1,defaultLabel:this.sandbox.translate("contact.accounts.termsOfDelivery.select"),valueName:"terms",repeatSelect:!1,preSelectedElements:[this.preselectedTermsOfDeliveryId],data:b}}])}.bind(this)).fail(function(a,b){this.sandbox.logger.error(a,b)}.bind(this))},copyArrayOfObjects:function(a){var b=[];return this.sandbox.util.foreach(a,function(a){b.push(this.sandbox.util.extend(!0,{},a))}.bind(this)),b},addDividerAndActionsForPaymentSelect:function(a){a.push({divider:!0}),a.push({id:-1,terms:this.sandbox.translate("public.edit-entries"),callback:this.showTermsOfPaymentOverlay.bind(this),updateLabel:!1})},addDividerAndActionsForDeliverySelect:function(a){a.push({divider:!0}),a.push({id:-1,terms:this.sandbox.translate("public.edit-entries"),callback:this.showTermsOfDeliveryOverlay.bind(this),updateLabel:!1})},showTermsOfDeliveryOverlay:function(){var a=this.sandbox.dom.$('<div id="'+c.overlayIdTermsOfDelivery+'"></div>'),b={instanceName:this.termsOfDeliveryInstanceName,el:c.overlaySelectorTermsOfDelivery,openOnStart:!0,removeOnClose:!0,triggerEl:null,title:this.sandbox.translate("public.edit-entries"),data:this.termsOfDeliveryData,valueName:"terms"};this.sandbox.dom.remove(c.overlaySelectorTermsOfDelivery),this.sandbox.dom.append(this.$el,a),this.sandbox.emit("sulu.types."+this.termsOfDeliveryInstanceName+".open",b)},showTermsOfPaymentOverlay:function(){var a=this.sandbox.dom.$('<div id="'+c.overlayIdTermsOfPayment+'"></div>'),b={instanceName:this.termsOfPaymentInstanceName,el:c.overlaySelectorTermsOfPayment,openOnStart:!0,removeOnClose:!0,triggerEl:null,title:this.sandbox.translate("public.edit-entries"),data:this.termsOfPaymentData,valueName:"terms"};this.sandbox.dom.remove(c.overlaySelectorTermsOfPayment),this.sandbox.dom.append(this.$el,a),this.sandbox.emit("sulu.types."+this.termsOfPaymentInstanceName+".open",b)},initForm:function(a){var b=this.sandbox.form.create(this.form);this.initBankAccountHandling(a),b.initialized.then(function(){this.setFormData(a),this.initTermsSelect(a),this.startTermsOfPaymentOverlay(),this.startTermsOfDeliveryOverlay()}.bind(this))},setFormData:function(a){this.sandbox.emit("sulu.contact-form.add-collectionfilters",this.form),this.sandbox.form.setData(this.form,a).then(function(){this.sandbox.start(this.form)}.bind(this)).fail(function(a){this.sandbox.logger.error("An error occured when setting data!",a)}.bind(this))},bindDomEvents:function(){this.sandbox.dom.keypress(this.form,function(a){13===a.which&&(a.preventDefault(),this.submit())}.bind(this))},bindCustomEvents:function(){this.sandbox.on("sulu.header.toolbar.delete",function(){this.sandbox.emit("sulu.contacts.account.delete",this.options.data.id)},this),this.sandbox.on("sulu.contacts.accounts.financials.saved",function(a){this.options.data=a,this.setFormData(a),this.setHeaderBar(!0)},this),this.sandbox.on("sulu.header.toolbar.save",function(){this.submit()},this),this.sandbox.on("sulu.header.back",function(){this.sandbox.emit("sulu.contacts.accounts.list")},this),this.sandbox.on("sulu.contact-form.added.bank-account",function(){this.numberOfBankAccounts++,this.updateBankAccountAddIcon(this.numberOfBankAccounts)},this),this.sandbox.on("sulu.contact-form.removed.bank-account",function(){this.numberOfBankAccounts--,this.updateBankAccountAddIcon(this.numberOfBankAccounts)},this),this.sandbox.on("sulu.types."+this.termsOfDeliveryInstanceName+".closed",function(a){var b=[];this.termsOfDeliveryData=this.copyArrayOfObjects(a),b.push(parseInt(this.selectedTermsOfDelivery?this.selectedTermsOfDelivery:this.preselectedTermsOfDeliveryId,10)),this.addDividerAndActionsForDeliverySelect(a),this.sandbox.util.foreach(a,function(a){a.terms=this.sandbox.translate(a.terms)}.bind(this)),this.sandbox.emit("husky.select."+this.termsOfDeliveryInstanceName+".update",a,b)},this),this.sandbox.on("sulu.types."+this.termsOfPaymentInstanceName+".closed",function(a){var b=[];this.termsOfPaymentData=this.copyArrayOfObjects(a),b.push(parseInt(this.selectedTermsOfPayment?this.selectedTermsOfPayment:this.preselectedTermsOfPaymentId,10)),this.addDividerAndActionsForPaymentSelect(a),this.sandbox.util.foreach(a,function(a){a.terms=this.sandbox.translate(a.terms)}.bind(this)),this.sandbox.emit("husky.select."+this.termsOfPaymentInstanceName+".update",a,b)},this)},submit:function(){if(this.sandbox.form.validate(this.form)){var a=this.sandbox.form.getData(this.form);this.sandbox.emit("sulu.contacts.accounts.financials.save",a)}},setHeaderBar:function(a){if(a!==this.saved){var b=this.options.data&&this.options.data.id?"edit":"add";this.sandbox.emit("sulu.header.toolbar.state.change",b,a,!0)}this.saved=a},listenForChange:function(){this.sandbox.dom.on(this.form,"change",function(){this.setHeaderBar(!1)}.bind(this),"select, input, textarea"),this.sandbox.dom.on(this.form,"keyup",function(){this.setHeaderBar(!1)}.bind(this),"input, textarea"),this.sandbox.on("sulu.contact-form.changed",function(){this.setHeaderBar(!1)}.bind(this)),this.sandbox.on("husky.select."+this.termsOfDeliveryInstanceName+".selected.item",function(a){a>0&&(this.selectedTermsOfDelivery=a,this.setHeaderBar(!1))}.bind(this)),this.sandbox.on("husky.select."+this.termsOfPaymentInstanceName+".selected.item",function(a){a>0&&(this.selectedTermsOfPayment=a,this.setHeaderBar(!1))}.bind(this))},initBankAccountHandling:function(b){this.numberOfBankAccounts=b.bankAccounts.length,this.updateBankAccountAddIcon(this.numberOfBankAccounts),this.sandbox.on("sulu.contact-form.initialized",function(){this.sandbox.emit("sulu.contact-form.add-collectionfilters",this.form);var c=this.sandbox.form.create(a);c.initialized.then(function(){this.setFormData(b)}.bind(this))}.bind(this)),this.sandbox.start([{name:"contact-form@sulucontact",options:{el:"#financials-form"}}])},updateBankAccountAddIcon:function(a){var b,e=this.sandbox.dom.find(c.bankAccountAddId);a&&a>0&&0===e.length?(b=this.sandbox.dom.createElement(d.addBankAccountsIcon),this.sandbox.dom.after(this.sandbox.dom.find(c.bankAccountsId),b)):0===a&&e.length>0&&this.sandbox.dom.remove(this.sandbox.dom.closest(e,c.addBankAccountsWrapper))}}});