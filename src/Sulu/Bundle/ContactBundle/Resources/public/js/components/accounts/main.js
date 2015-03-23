/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

define([
    'sulucontact/model/account',
    'sulucontact/model/contact',
    'sulucontact/model/accountContact',
    'accountsutil/header',
    'sulucontact/model/termsOfPayment',
    'sulucontact/model/termsOfDelivery',
    'sulucontact/model/email',
    'sulucontact/model/emailType',
    'sulumedia/model/media',
    'sulucategory/model/category',
    'accountsutil/delete-dialog'
], function(
    Account,
    Contact,
    AccountContact,
    AccountsUtilHeader,
    TermsOfPayment,
    TermsOfDelivery,
    Email,
    EmailType,
    Media,
    Category,
    DeleteDialog) {

    'use strict';

    var templates = {
        dialogEntityFoundTemplate: [
            '<p><%= foundMessage %>:</p>',
            '<% if (typeof list !== "undefined") { %>',
            '<ul><%= list %></ul>',
            '<% } %>',
            '<% if (typeof numChildren !== "undefined" && numChildren > 3 && typeof andMore !== "undefined") { %>',
            '<p><%= andMore %></p>',
            '<% } %>',
            '<p><%= description %></p>',
            '<% if (typeof checkboxText !== "undefined") { %>',
            '<p>',
            '   <label for="overlay-checkbox">',
            '       <div class="custom-checkbox">',
            '           <input type="checkbox" id="overlay-checkbox" class="form-element" />',
            '           <span class="icon"></span>',
            '       </div>',
            '       <%= checkboxText %>',
            '</label>',
            '</p>',
            '<% } %>'
        ].join('')

    };

    return {

        initialize: function() {
            this.bindCustomEvents();
            this.bindSidebarEvents();
            this.account = null;
            this.accountType = null;
            this.accountTypes = null;

            if (this.options.display === 'list') {
                this.renderList();

            } else if (this.options.display === 'form') {
                this.renderForm().then(function() {
                        AccountsUtilHeader.setHeader.call(this, this.account, this.options.accountType);
                    }.bind(this)
                );
            } else if (this.options.display === 'contacts') {
                this.renderComponent(
                    'accounts/components/',
                    this.options.display,
                    'accounts-form-container', {}
                ).then(function() {
                        AccountsUtilHeader.setHeader.call(this, this.account, this.options.accountType);
                    }.bind(this)
                );
            } else if (this.options.display === 'financials') {
                this.renderComponent(
                    'accounts/components/',
                    this.options.display,
                    'accounts-form-container',
                    {}
                ).then(function() {
                        AccountsUtilHeader.setHeader.call(this, this.account, this.options.accountType);
                    }.bind(this)
                );
            } else if (this.options.display === 'documents') {
                this.renderComponent(
                    '',
                    this.options.display,
                    'documents-form',
                    {type: 'account'}
                ).then(function() {
                        AccountsUtilHeader.setHeader.call(this, this.account, this.options.accountType);
                    }.bind(this)
                );
            } else {
                throw 'display type wrong';
            }
        },

        bindCustomEvents: function() {
            // delete contact
            this.sandbox.on('sulu.contacts.account.delete', this.del.bind(this));

            // save the current package
            this.sandbox.on('sulu.contacts.accounts.save', this.save.bind(this));

            // wait for navigation events
            this.sandbox.on('sulu.contacts.accounts.load', this.load.bind(this));

            // wait for navigation events
            this.sandbox.on('sulu.contacts.contact.load', this.loadContact.bind(this));

            // add new contact
            this.sandbox.on('sulu.contacts.accounts.new', this.add.bind(this));

            // delete selected contacts
            this.sandbox.on('sulu.contacts.accounts.delete', this.delAccounts.bind(this));

            // adds a new accountContact Relation
            this.sandbox.on('sulu.contacts.accounts.contact.save', this.addAccountContact.bind(this));

            // removes accountContact Relation
            this.sandbox.on('sulu.contacts.accounts.contacts.remove', this.removeAccountContacts.bind(this));

            // set main contact
            this.sandbox.on('sulu.contacts.accounts.contacts.set-main', this.setMainContact.bind(this));

            // saves financial infos
            this.sandbox.on('sulu.contacts.accounts.financials.save', this.saveFinancials.bind(this));

            // load list view
            this.sandbox.on('sulu.contacts.accounts.list', function(type, noReload) {
                var typeString = '';
                if (!!type) {
                    typeString = '/type:' + type;
                }
                this.sandbox.emit(
                    'sulu.router.navigate', 'contacts/accounts' + typeString,
                    !noReload ? true : false,
                    true,
                    true
                );
            }, this);

            this.sandbox.on('sulu.contacts.account.types', function(data) {
                this.accountType = data.accountType;
                this.accountTypes = data.accountTypes;
            }.bind(this));

            this.sandbox.on('sulu.contacts.account.get.types', function(callback) {
                if (typeof callback === 'function') {
                    callback(this.accountType, this.accountTypes);
                }
            }.bind(this));

            this.sandbox.on('sulu.contacts.account.convert', function(data) {
                this.convertAccount(data);
            }.bind(this));

            // handling of terms of delivery/payment eventlistener
            this.sandbox.on('husky.select.terms-of-delivery.delete', this.deleteTerms.bind(this, 'delivery'));
            this.sandbox.on('husky.select.terms-of-payment.delete', this.deleteTerms.bind(this, 'payment'));
            this.sandbox.on('husky.select.terms-of-delivery.save', this.saveTerms.bind(this, 'delivery'));
            this.sandbox.on('husky.select.terms-of-payment.save', this.saveTerms.bind(this, 'payment'));

            // handling documents
            this.sandbox.on('sulu.contacts.accounts.medias.save', this.saveDocuments.bind(this));

            // receive form of address values via template
            this.sandbox.on('sulu.contacts.set-types', function(types) {
                this.formOfAddress = types.formOfAddress;
                this.emailTypes = types.emailTypes;
            }.bind(this));

            // pass them on to the contact tab when fully loaded
            this.sandbox.on('sulu.contacts.accounts.contacts.initialized', function() {
                this.sandbox.emit('sulu.contacts.accounts.set-form-of-address', this.formOfAddress);
            }.bind(this));

            // add a new contact
            this.sandbox.on('sulu.contacts.accounts.new.contact', this.createNewContact.bind(this));
        },

        /**
         * adds a new contact and assigns the current account to it
         */
        createNewContact: function(data) {
            var contact = new Contact(data);
            contact.set('emails', [
                new Email({
                    email: data.email,
                    emailType: EmailType.findOrCreate({id: this.emailTypes[0].id})
                })
            ]);
            contact.save(null, {
                success: function(response) {
                    var model = response.toJSON();
                    this.sandbox.emit('sulu.contacts.accounts.contact.created', model);
                }.bind(this),
                error: function() {
                    this.sandbox.logger.log("error while saving a new contact");
                }.bind(this)
            });
        },

        saveDocuments: function(accountId, newMediaIds, removedMediaIds) {
            this.sandbox.emit('sulu.header.toolbar.item.loading', 'save-button');

            this.sandbox.logger.warn('newMediaIds',newMediaIds);
            this.sandbox.logger.warn('removedMediaIds',removedMediaIds);

            this.processAjaxForDocuments(newMediaIds, accountId, 'POST');
            this.processAjaxForDocuments(removedMediaIds, accountId, 'DELETE');
        },

        processAjaxForDocuments: function(mediaIds, accountId, type){

            var requests=[],
                medias=[],
                url;

            if(mediaIds.length > 0) {
                this.sandbox.util.each(mediaIds, function(index, id) {

                    if(type === 'DELETE') {
                        url = '/admin/api/accounts/' + accountId + '/medias/' + id;
                    } else if(type === 'POST') {
                        url = '/admin/api/accounts/' + accountId + '/medias';
                    }

                    requests.push(
                        this.sandbox.util.ajax({
                            url: url,
                            data: {mediaId: id},
                            type: type
                        }).fail(function() {
                            this.sandbox.logger.error("Error while saving documents!");
                        }.bind(this))
                    );
                    medias.push(id);
                }.bind(this));

                this.sandbox.util.when.apply(null, requests).then(function() {
                    if(type === 'DELETE') {
                        this.sandbox.logger.warn(medias);
                        this.sandbox.emit('sulu.contacts.contacts.medias.removed', medias);
                    } else if(type === 'POST') {
                        this.sandbox.logger.warn(medias);
                        this.sandbox.emit('sulu.contacts.contacts.medias.saved', medias);
                    }
                }.bind(this));
            }
        },

        deleteTerms: function(termsKey, ids) {
            var condition, clazz, instanceName;
            if (!!ids && ids.length > 0) {

                if (termsKey === 'delivery') {
                    clazz = TermsOfDelivery;
                    instanceName = 'terms-of-delivery';
                } else if (termsKey === 'payment') {
                    clazz = TermsOfPayment;
                    instanceName = 'terms-of-payment';
                }

                this.sandbox.util.each(ids, function(index, id) {
                    condition = clazz.findOrCreate({id: id});
                    condition.destroy({
                        error: function() {
                            this.sandbox.emit(
                                    'husky.select.' + instanceName + '.revert'
                            );
                        }.bind(this)
                    });
                }.bind(this));
            }
        },

        saveTerms: function(termsKey, data) {
            var instanceName, urlSuffix;

            if (!!data && data.length > 0) {
                if (termsKey === 'delivery') {
                    urlSuffix = 'termsofdeliveries';
                    instanceName = 'terms-of-delivery';
                } else if (termsKey === 'payment') {
                    urlSuffix = 'termsofpayments';
                    instanceName = 'terms-of-payment';
                }

                this.sandbox.util.save(
                        '/admin/api/' + urlSuffix,
                    'PATCH',
                    data)
                    .then(function(response) {
                        this.sandbox.emit('husky.select.' + instanceName + '.update',
                            response,
                            null,
                            true);
                    }.bind(this)).fail(function(status, error) {
                        this.sandbox.emit(
                                'husky.select.' + instanceName + '.save.revert'
                        );
                        this.sandbox.logger.error(status, error);
                    }.bind(this));
            }
        },

        /**
         * Binds general sidebar events
         */
        bindSidebarEvents: function() {
            this.sandbox.dom.off('#sidebar');

            this.sandbox.dom.on('#sidebar', 'click', function(event) {
                var id = this.sandbox.dom.data(event.currentTarget, 'id');
                this.sandbox.emit('sulu.contacts.accounts.load', id);
            }.bind(this), '#sidebar-accounts-list');

            this.sandbox.dom.on('#sidebar', 'click', function(event) {
                var id = this.sandbox.dom.data(event.currentTarget, 'id');
                this.sandbox.emit('sulu.router.navigate', 'contacts/contacts/edit:' + id + '/details');
                this.sandbox.emit('husky.navigation.select-item', 'contacts/contacts');
            }.bind(this), '#main-contact');
        },

        /**
         * loads contact by id
         */
        getAccount: function(id) {
            this.account = new Account({id: id});
            this.account.fetch({
                success: function(model) {
                    this.account = model;
                    this.dfdAccount.resolve();
                }.bind(this),
                error: function() {
                    this.sandbox.logger.log('error while fetching contact');
                }.bind(this)
            });
        },

        /**
         * loads system members
         */
        getSystemMembers: function() {
            this.sandbox.util.load('api/contacts?bySystem=true')
                .then(function(response) {
                    this.responsiblePersons = response._embedded.contacts;
                    this.sandbox.util.foreach(this.responsiblePersons, function(el) {
                        var contact = Contact.findOrCreate(el);
                        el = contact.toJSON();
                    }.bind(this));
                    this.dfdSystemContacts.resolve();
                }.bind(this))
                .fail(function(textStatus, error) {
                    this.sandbox.logger.error(textStatus, error);
                }.bind(this));
        },

        // sets main contact
        setMainContact: function(id) {
            // set mainContact
            this.account.set({mainContact: Contact.findOrCreate({id: id})});
            this.account.save(null, {
                patch: true,
                success: function() {
                    // TODO: show success label
                }.bind(this)
            });
        },

        addAccountContact: function(id, position) {
            // set id to contacts id;
            var accountContact = AccountContact.findOrCreate({
                id: id,
                contact: Contact.findOrCreate({id: id}), account: this.account});

            if (!!position) {
                accountContact.set({position: position});
            }

            accountContact.save(null, {
                // on success save contacts id
                success: function(response) {
                    var model = response.toJSON();
                    this.sandbox.emit('sulu.contacts.accounts.contact.saved', model);
                }.bind(this),
                error: function() {
                    this.sandbox.logger.log("error while saving contact");
                }.bind(this)
            });
        },

        /**
         * removes mulitple AccountContacts
         * @param ids
         */
        removeAccountContacts: function(ids) {
            // show warning
            this.sandbox.emit('sulu.overlay.show-warning', 'sulu.overlay.be-careful', 'sulu.overlay.delete-desc', null, function() {
                // get ids of selected contacts
                var accountContact;
                this.sandbox.util.foreach(ids, function(id) {
                    // set account and contact as well as  id to contacts id(so that request is going to be sent)
                    accountContact = AccountContact.findOrCreate({id: id, contact: Contact.findOrCreate({id: id}), account: this.account});
                    accountContact.destroy({
                        success: function() {
                            this.sandbox.emit('sulu.contacts.accounts.contacts.removed', id);
                        }.bind(this),
                        error: function() {
                            this.sandbox.logger.log("error while deleting AccountContact");
                        }.bind(this)
                    });
                }.bind(this));
            }.bind(this));
        },

        /**
         * Converts an account
         */
        convertAccount: function(data) {
            this.confirmConversionDialog(function(wasConfirmed) {
                if (wasConfirmed) {
                    this.account.set({type: data.id});
                    this.sandbox.emit('sulu.header.toolbar.item.loading', 'options-button');
                    this.sandbox.util.ajax('/admin/api/accounts/' + this.account.id + '?action=convertAccountType&type=' + data.name, {

                        type: 'POST',

                        success: function(response) {
                            var model = response;
                            this.sandbox.emit('sulu.header.toolbar.item.enable', 'options-button');

                            // update tabs and breadcrumb
                            this.sandbox.emit('sulu.contacts.accounts.saved', model);
                            AccountsUtilHeader.setHeader.call(this, this.account, this.options.accountType);

                            // update toolbar
                            this.sandbox.emit('sulu.account.type.converted');
                        }.bind(this),

                        error: function() {
                            this.sandbox.logger.log("error while saving profile");
                        }.bind(this)
                    });
                }
            }.bind(this));
        },

        /**
         * @var ids - array of ids to delete
         * @var callback - callback function returns true or false if data got deleted
         */
        confirmConversionDialog: function(callbackFunction) {

            // check if callback is a function
            if (!!callbackFunction && typeof(callbackFunction) !== 'function') {
                throw 'callback is not a function';
            }

            // show dialog
            this.sandbox.emit('sulu.overlay.show-warning',
                'sulu.overlay.be-careful',
                'contact.accounts.type.conversion.message',
                callbackFunction.bind(this, false),
                callbackFunction.bind(this, true)
            );
        },

        // show confirmation and delete account
        del: function() {
            DeleteDialog.showForSingle(this.sandbox, this.account, this.options.id);
        },

        // saves an account
        save: function(data) {
            this.sandbox.emit('sulu.header.toolbar.item.loading', 'save-button');

            this.account.set(data);

            this.account.get('categories').reset();
            this.sandbox.util.foreach(data.categories,function(id){
                var category = Category.findOrCreate({id: id});
                this.account.get('categories').add(category);
            }.bind(this));

            this.account.save(null, {
                // on success save contacts id
                success: function(response) {
                    var model = response.toJSON();
                    if (!!data.id) {
                        this.sandbox.emit('sulu.contacts.accounts.saved', model);
                    } else {
                        this.sandbox.emit('sulu.router.navigate', 'contacts/accounts/edit:' + model.id + '/details');
                    }
                }.bind(this),
                error: function() {
                    this.sandbox.logger.log("error while saving profile");
                }.bind(this)
            });
        },

        // saves financial infos
        saveFinancials: function(data) {
            this.sandbox.emit('sulu.header.toolbar.item.loading', 'save-button');

            this.account.set(data);

            // set correct backbone models
            if (!!data.termsOfPayment) {
                this.account.set(
                    'termsOfPayment',
                    TermsOfPayment.findOrCreate({id: data.termsOfPayment})
                );
            }
            if (!!data.termsOfDelivery) {
                this.account.set(
                    'termsOfDelivery',
                    TermsOfDelivery.findOrCreate({id: data.termsOfDelivery})
                );
            }

            this.account.save(null, {
                patch: true,
                success: function(response) {
                    var model = response.toJSON();
                    this.sandbox.emit('sulu.contacts.accounts.financials.saved', model);
                }.bind(this),
                error: function() {
                    this.sandbox.logger.log("error while saving profile");
                }.bind(this)
            });
        },

        load: function(id) {
            // TODO: show loading icon
            this.sandbox.emit('sulu.router.navigate', 'contacts/accounts/edit:' + id + '/details');
        },

        loadContact: function(id) {
            // TODO: show loading icon
            this.sandbox.emit('sulu.router.navigate', 'contacts/contacts/edit:' + id + '/details');
        },

        add: function(type) {
            // TODO: show loading icon
            this.sandbox.emit('sulu.router.navigate', 'contacts/accounts/add/type:' + type);

        },

        delAccounts: function(ids) {
            if (ids.length < 1) {
                // TODO: translations
                this.sandbox.emit('sulu.overlay.show-error', 'sulu.overlay.delete-no-items');
                return;
            }
            this.showDeleteConfirmation(ids);
        },

        renderList: function() {
            var $list = this.sandbox.dom.createElement('<div id="accounts-list-container"/>');
            this.html($list);
            this.sandbox.start([
                {
                    name: 'accounts/components/list@sulucontact',
                    options: {
                        el: $list,
                        accountType: this.options.accountType ? this.options.accountType : null
                    }
                }
            ]);
        },

        /**
         * Adds a container with the given id and starts a component with the given name in it
         * @param path path to component
         * @param componentName
         * @param containerId
         * @param params additional params
         * @returns {*}
         */
        renderComponent: function(path, componentName, containerId, params) {
            var $form = this.sandbox.dom.createElement('<div id="' + containerId + '"/>'),
                dfd = this.sandbox.data.deferred();

            this.html($form);

            if (!!this.options.id) {
                this.account = new Account({id: this.options.id});
                this.account.fetch({
                    success: function(model) {
                        this.account = model;
                        this.sandbox.start([
                            {
                                name: path + componentName + '@sulucontact',
                                options: {
                                    el: $form,
                                    data: model.toJSON(),
                                    params: !!params ? params : {}
                                }
                            }
                        ]);
                        dfd.resolve();
                    }.bind(this),
                    error: function() {
                        this.sandbox.logger.log("error while fetching contact");
                        dfd.reject();
                    }.bind(this)
                });
            }
            return dfd.promise();
        },

        renderForm: function() {
            // load data and show form
            this.account = new Account();

            var accTypeId,
                $form = this.sandbox.dom.createElement('<div id="accounts-form-container"/>'),
                dfd = this.sandbox.data.deferred();
            this.html($form);

            if (!!this.options.id) {
                this.account = new Account({id: this.options.id});
                //account = this.getModel(this.options.id);
                this.account.fetch({
                    success: function(model) {
                        this.sandbox.start([
                            {name: 'accounts/components/form@sulucontact', options: { el: $form, data: model.toJSON()}}
                        ]);
                        dfd.resolve();
                    }.bind(this),
                    error: function() {
                        this.sandbox.logger.log("error while fetching contact");
                        dfd.reject();
                    }.bind(this)
                });
            } else {
                accTypeId = AccountsUtilHeader.getAccountTypeIdByTypeName.call(this, this.options.accountType);
                this.account.set({type: accTypeId});
                this.sandbox.start([
                    {name: 'accounts/components/form@sulucontact', options: { el: $form, data: this.account.toJSON()}}
                ]);
                dfd.resolve();
            }
            return dfd.promise();
        },

        showDeleteConfirmation: function(ids) {
            if (ids.length === 0) {
                return;
            } else if (ids.length === 1) {
                // if only one account was selected - get related sub-companies and contacts (and show the first 3 ones)
                //this.confirmSingleDeleteDialog(ids[0], callbackFunction);
                DeleteDialog.showForSingle(this.sandbox, Account.findOrCreate({id:ids[0]}), ids[0], true)
            } else {
                // if multiple accounts were selected, get related sub-companies and show simplified message
                //this.confirmMultipleDeleteDialog(ids, callbackFunction);
                DeleteDialog.showForMultiple(this.sandbox, ids);
            }
        }
    };
});
