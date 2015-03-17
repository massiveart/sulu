define(["config","widget-groups"],function(a,b){"use strict";var c="#contact-form",d=["urls","emails","faxes","phones","notes"],e={tagsId:"#tags",addressAddId:"#address-add",addAddressWrapper:".grid-row",addressesSelector:"#addresses"},f=function(){this.sandbox.emit("sulu.header.set-toolbar",{template:"default"})};return function(){return{view:!0,layout:function(){return{content:{width:b.exists("contact-detail")?"max":"fixed"},sidebar:{width:"fixed",cssClasses:"sidebar-padding-50"}}},templates:["/admin/contact/template/contact/form"],customTemplates:{addAddressesIcon:['<div class="grid-row">','    <div class="grid-col-12">','       <span id="address-add" class="fa-plus-circle icon address-add clickable pointer m-left-140"></span>',"   </div>","</div>"].join("")},initialize:function(){this.saved=!0,this.formId="#contact-form",this.autoCompleteInstanceName="accounts-",this.dfdAllFieldsInitialized=this.sandbox.data.deferred(),this.dfdListenForChange=this.sandbox.data.deferred(),this.dfdFormIsSet=this.sandbox.data.deferred(),this.dfdBirthdayIsSet=this.sandbox.data.deferred(),this.sandbox.data.when(this.dfdListenForChange,this.dfdBirthdayIsSet).then(function(){this.dfdAllFieldsInitialized.resolve()}.bind(this)),this.setTitle(),this.render(),f.call(this),this.listenForChange(),this.options.data&&this.options.data.id&&b.exists("contact-detail")&&this.initSidebar("/admin/widget-groups/contact-detail?contact=",this.options.data.id),this.setHeaderBar(!0)},initSidebar:function(a,b){this.sandbox.emit("sulu.sidebar.set-widget",a+b)},render:function(){this.sandbox.once("sulu.contacts.set-defaults",this.setDefaults.bind(this)),this.sandbox.once("sulu.contacts.set-types",this.setTypes.bind(this)),this.sandbox.dom.html(this.$el,this.renderTemplate("/admin/contact/template/contact/form")),this.sandbox.on("husky.dropdown.type.item.click",this.typeClick.bind(this));var a=this.initContactData();this.companyInstanceName="companyContact"+a.id,this.initForm(a),this.setTags(a),this.bindDomEvents(),this.bindCustomEvents(),this.bindTagEvents(a)},setTitle:function(){var a=this.sandbox.translate("contact.contacts.title"),b=[{title:"navigation.contacts"},{title:"contact.contacts.title",event:"sulu.contacts.contacts.list"}];this.options.data&&this.options.data.id&&(a=this.options.data.fullName,b.push({title:"#"+this.options.data.id})),this.sandbox.emit("sulu.header.set-title",a),this.sandbox.emit("sulu.header.set-breadcrumb",b)},setTags:function(){var a=this.sandbox.util.uniqueId();this.options.data.id&&(a+="-"+this.options.data.id),this.autoCompleteInstanceName+=a,this.dfdFormIsSet.then(function(){this.sandbox.start([{name:"auto-complete-list@husky",options:{el:"#tags",instanceName:this.autoCompleteInstanceName,getParameter:"search",itemsKey:"tags",remoteUrl:"/admin/api/tags?flat=true&sortBy=name&searchFields=name",completeIcon:"tag",noNewTags:!0}}])}.bind(this))},bindTagEvents:function(a){a.tags&&a.tags.length>0?(this.sandbox.on("husky.auto-complete-list."+this.autoCompleteInstanceName+".initialized",function(){this.sandbox.emit("husky.auto-complete-list."+this.autoCompleteInstanceName+".set-tags",a.tags)}.bind(this)),this.sandbox.on("husky.auto-complete-list."+this.autoCompleteInstanceName+".items-added",function(){this.dfdListenForChange.resolve()}.bind(this))):this.dfdListenForChange.resolve()},setDefaults:function(a){this.defaultTypes=a},setTypes:function(a){this.fieldTypes=a},setFormData:function(a,b){this.sandbox.emit("sulu.contact-form.add-collectionfilters",c),this.sandbox.form.setData(c,a).then(function(){this.sandbox.start(b?c:"#contact-fields"),this.sandbox.emit("sulu.contact-form.add-required",["email"]),this.sandbox.emit("sulu.contact-form.content-set"),this.dfdFormIsSet.resolve()}.bind(this)).fail(function(a){this.sandbox.logger.error("An error occured when setting data!",a)}.bind(this))},initForm:function(b){var d=a.get("sulucontact.components.autocomplete.default.account");d.el="#company",d.value=b.account?b.account:"",d.instanceName=this.companyInstanceName,this.sandbox.start([{name:"auto-complete@husky",options:d}]),this.numberOfAddresses=b.addresses.length,this.updateAddressesAddIcon(this.numberOfAddresses),this.sandbox.on("sulu.contact-form.initialized",function(){var a=this.sandbox.form.create(c);a.initialized.then(function(){this.setFormData(b,!0)}.bind(this))}.bind(this)),this.sandbox.start([{name:"contact-form@sulucontact",options:{el:"#contact-edit-form",fieldTypes:this.fieldTypes,defaultTypes:this.defaultTypes}}])},updateAddressesAddIcon:function(a){var b,c=this.sandbox.dom.find(e.addressAddId);a&&a>0&&0===c.length?(b=this.sandbox.dom.createElement(this.customTemplates.addAddressesIcon),this.sandbox.dom.after(this.sandbox.dom.find("#addresses"),b)):0===a&&c.length>0&&(this.sandbox.dom.remove(this.sandbox.dom.closest(c,e.addAddressWrapper)),this.sandbox.emit("sulu.contact-form.update.addAddressLabel",e.addressesSelector))},bindDomEvents:function(){},bindCustomEvents:function(){this.sandbox.on("sulu.contact-form.added.address",function(){this.numberOfAddresses++,this.updateAddressesAddIcon(this.numberOfAddresses)},this),this.sandbox.on("sulu.contact-form.removed.address",function(){this.numberOfAddresses--,this.updateAddressesAddIcon(this.numberOfAddresses)},this),this.sandbox.on("sulu.header.toolbar.delete",function(){this.sandbox.emit("sulu.contacts.contact.delete",this.options.data.id)},this),this.sandbox.on("sulu.contacts.contacts.saved",function(a){this.options.data=a,this.initContactData(),this.setFormData(a),this.setHeaderBar(!0)},this),this.sandbox.on("sulu.header.toolbar.save",function(){this.submit()},this),this.sandbox.on("sulu.header.back",function(){this.sandbox.emit("sulu.contacts.contacts.list")},this),this.sandbox.on("husky.input.birthday.initialized",function(){this.dfdBirthdayIsSet.resolve()},this),this.sandbox.once("husky.select.position-select.initialize",function(){this.sandbox.dom.find("#"+this.companyInstanceName).val()||this.enablePositionDropdown(!1)},this)},initContactData:function(){var a=this.options.data;return this.sandbox.util.foreach(d,function(b){a.hasOwnProperty(b)||(a[b]=[])}),a.emails=this.fillFields(a.emails,1,{id:null,email:"",emailType:this.defaultTypes.emailType}),a.phones=this.fillFields(a.phones,1,{id:null,phone:"",phoneType:this.defaultTypes.phoneType}),a.faxes=this.fillFields(a.faxes,1,{id:null,fax:"",faxType:this.defaultTypes.faxType}),a.notes=this.fillFields(a.notes,1,{id:null,value:""}),a.urls=this.fillFields(a.urls,0,{id:null,url:"",urlType:this.defaultTypes.urlType}),a},typeClick:function(a,b){this.sandbox.logger.log("email click",a),b.find("*.type-value").data("element").setValue(a)},fillFields:function(a,b,c){var d,e=-1,f=a.length;for(b>f&&(f=b);++e<f;)d=e+1>b?{}:{permanent:!0},a[e]?a[e].attributes=d:(a.push(c),a[a.length-1].attributes=d);return a},submit:function(){if(this.sandbox.logger.log("save Model"),this.sandbox.form.validate(c)){var a=this.sandbox.form.getData(c);""===a.id&&delete a.id,a.tags=this.sandbox.dom.data(this.$find(e.tagsId),"tags"),a.account={id:this.sandbox.dom.attr("#"+this.companyInstanceName,"data-id")},this.sandbox.logger.log("log data",a),this.sandbox.emit("sulu.contacts.contacts.save",a)}},setHeaderBar:function(a){if(a!==this.saved){var b=this.options.data&&this.options.data.id?"edit":"add";this.sandbox.emit("sulu.header.toolbar.state.change",b,a,!0)}this.saved=a},initializeDropDownListender:function(a){var b="husky.select."+a;this.sandbox.on(b+".selected.item",function(a){a>0&&this.setHeaderBar(!1)}.bind(this)),this.sandbox.on(b+".deselected.item",function(){this.setHeaderBar(!1)}.bind(this))},enablePositionDropdown:function(a){this.sandbox.emit(a?"husky.select.position-select.enable":"husky.select.position-select.disable")},listenForChange:function(){this.sandbox.data.when(this.dfdAllFieldsInitialized).then(function(){this.sandbox.dom.on("#contact-form","change",function(){this.setHeaderBar(!1)}.bind(this),".changeListener select, .changeListener input, .changeListener textarea"),this.sandbox.dom.on("#contact-form","keyup",function(){this.setHeaderBar(!1)}.bind(this),".changeListener select, .changeListener input, .changeListener textarea"),this.sandbox.on("sulu.contact-form.changed",function(){this.setHeaderBar(!1)}.bind(this)),this.sandbox.dom.on("#company","keyup",function(a){a.target.value||this.enablePositionDropdown(!1)}.bind(this)),this.companySelected="husky.auto-complete."+this.companyInstanceName+".select",this.sandbox.on(this.companySelected,function(){this.enablePositionDropdown(!0)}.bind(this))}.bind(this)),this.sandbox.on("husky.select.form-of-address.selected.item",function(){this.setHeaderBar(!1)}.bind(this)),this.initializeDropDownListender("title-select","api/contact/titles"),this.initializeDropDownListender("position-select","api/contact/positions")}}}()});