(function()
{

    PhpGenAdmin.Utils =
    {
        showProgressCursor: function()
        {
            $('body').addClass('cursor-progress');
        },
        
        hideProgressCursor: function()
        {
            $('body').removeClass('cursor-progress');
        }
    };

    PhpGenAdmin.Api = new Class({
        initialize: function(apiUrl)
        {
            this.apiUrl = apiUrl;
        },

        _invokeApiFunction: function(functionShortName, data)
        {
            var result = new $.Deferred();

            $.get(
                this.apiUrl,
                $.extend({},
                    { 'hname': functionShortName },
                    data))
            .success(
                function(data)
                {
                    if (data.status == 'error')
                        result.reject(data.result);
                    else
                    {
                        if (data.result)
                            result.resolve(data.result);
                        else
                            result.resolve();
                    }
                })
            .error(
                function(xhr, status, errorMessage)
                {
                    result.reject(status + ': ' + errorMessage);
                });

            return result.promise();
        },

        addUser: function(id, name, password)
        {
            return this._invokeApiFunction('au',
                {
                    'id': id,
                    'username': name,
                    'password': password
                });
        },

        removeUser: function(id) {
            return this._invokeApiFunction('ru',
                {
                    'user_id': id
                });
        },

        changeUserName: function(id, username)
        {
            return this._invokeApiFunction('eu',
                {
                    'user_id': id,
                    'username': username
                });
        },

        changeUserPassword: function(id, password)
        {
            return this._invokeApiFunction('cup',
                {
                    'user_id': id,
                    'password': password
                });
        },

        getUserGrants: function(userId)
        {
            return this._invokeApiFunction('gug',
                {
                    'user_id': userId
                });
        },

        addUserGrant: function(userId, pageName, grantName)
        {
            return this._invokeApiFunction('aug',
                {
                    'user_id': userId,
                    'page_name': pageName,
                    'grant': grantName
                });
        },

        removeUserGrant: function(userId, pageName, grantName)
        {
            return this._invokeApiFunction('rug',
                {
                    'user_id': userId,
                    'page_name': pageName,
                    'grant': grantName
                });
        }

    });

    PhpGenAdmin.UserViewModel = new Class({
        initialize: function(api, id, name, password, editable)
        {
            this.api = api;
            this.id = id;
            this.name = ko.observable(name);
            this.password = password;
            this.editable = ko.observable(editable);
            this.grantsLoaded = ko.observable(false);
            this.grantsExpanded = ko.observable(false);
            this.grants = ko.observableArray([]);
        },

        _loadGrants: function()
        {
            this.api.getUserGrants(this.id).done(function(grants)
            {
                for (var i = 0; i < grants.length; i++)
                {
                    this.grants.push(
                        new PhpGenAdmin.UserPageGrantsViewModel(
                            this.api,
                            this.id,
                            grants[i].name,
                            grants[i].caption,
                            grants[i].selectGrant,
                            grants[i].updateGrant,
                            grants[i].insertGrant,
                            grants[i].deleteGrant,
                            grants[i].adminGrant
                        )
                    );
                }
                this.grantsLoaded(true);
            }.bind(this));
        },

        collapseGrants: function()
        {
            this.grantsExpanded(false);
        },

        expandGrants: function()
        {
            if (!this.grantsExpanded())
            {
                this.grantsExpanded(true);
                if (!this.grantsLoaded())
                    this._loadGrants();
            }
        },

        toggleGrantsExpanded: function()
        {
            if (!this.grantsExpanded())
            {
                this.grantsExpanded(true);
                if (!this.grantsLoaded())
                    this._loadGrants();
            }
            else
            {
                this.grantsExpanded(false);
            }
        }
    });

    PhpGenAdmin.UserPageGrantsViewModel = new Class({
        initialize: function(api, userId, pageName, caption, selectGrant, updateGrant, insertGrant, deleteGrant, adminGrant)
        {
            this.api = api;
            this.userId = userId;
            this.pageName = ko.observable(pageName);
            this.caption = ko.observable(caption);
            this.selectGrant = ko.observable(selectGrant);
            this.updateGrant = ko.observable(updateGrant);
            this.insertGrant = ko.observable(insertGrant);
            this.deleteGrant = ko.observable(deleteGrant);
            this.adminGrant = ko.observable(adminGrant);

            this.selectGrant.subscribe(function(newValue) { this._updateUserGrant('SELECT', newValue) }.bind(this));
            this.updateGrant.subscribe(function(newValue) { this._updateUserGrant('UPDATE', newValue) }.bind(this));
            this.insertGrant.subscribe(function(newValue) { this._updateUserGrant('INSERT', newValue) }.bind(this));
            this.deleteGrant.subscribe(function(newValue) { this._updateUserGrant('DELETE', newValue) }.bind(this));
            this.adminGrant.subscribe(function(newValue) { this._updateUserGrant('ADMIN', newValue) }.bind(this));
        },

        _updateUserGrant: function(grantName, newValue)
        {
            PhpGenAdmin.Utils.showProgressCursor();
            (function()
            {
                if (newValue)
                    return this.api.addUserGrant(this.userId, this.pageName(), grantName);
                else
                    return this.api.removeUserGrant(this.userId, this.pageName(), grantName);
            }.bind(this))()
                .always(function()
                {
                    PhpGenAdmin.Utils.hideProgressCursor();
                })
                .fail(function(message) {
                    bootbox.animate(false);
                    bootbox.alert(message);
                });
                
        }
    });

    PhpGenAdmin.AdminPanelViewModel = function(api){
        this.api = api;

        this.newUser =
        {
            id: ko.observable(''),
            name: ko.observable('New user'),
            password: ko.observable('')
        };

        this.changePasswordUser =
        {
            id: ko.observable(''),
            name: ko.observable('New user'),
            password: ko.observable('')
        };

        this.editUser =
        {
            id: ko.observable(''),
            name: ko.observable('Edit user'),
            password: ko.observable('')
        };

        this.currentUserRoles = ko.observableArray([]);

        this.invokeRemoveUserDialog = function(user)
        {
            PhpGenAdmin.Utils.showProgressCursor();
            this.api.removeUser(user.id)
                .done(function(result)
                {
                    this.users.remove(user);
                }.bind(this))
                .always(function()
                {
                    PhpGenAdmin.Utils.hideProgressCursor();
                }).fail(function(message)
                {
                    alert(message);
                });
        };

        this.invokeChangeUserPasswordDialog = function(user)
        {
            var self = this;
            var dialog = $("#pg-admin-change-user-password-dialog");

            self.changePasswordUser.id(user.id);
            self.changePasswordUser.name(user.name());
            self.changePasswordUser.password('');

            $('#save-change-password-dialog').bind('click', function(e) {
                e.preventDefault();

                self.api.changeUserPassword(self.changePasswordUser.id(), self.changePasswordUser.password())
                    .fail(function(message)
                    {
                        alert(message);
                    })
                    .done(_.bind(function(result)
                    {
                        alert('Password changed');
                        $(this).dialog("close");
                    }, this));

                dialog.modal('hide');
            });

            dialog.modal('show');
            dialog.bind('hidden', function() {
                $('#save-change-password-dialog').unbind('click');
                dialog.unbind('hidden');
            });

/*

            dialog.dialog("widget").find("div.ui-dialog-buttonpane").remove();
            dialog.dialog("option", "buttons", {
                "Change password": function()
                {
                    self.api.changeUserPassword(self.changePasswordUser.id(), self.changePasswordUser.password())
                        .fail(function(message)
                        {
                            alert(message);
                        })
                        .done(function(result)
                        {
                            alert('Password changed');
                            $(this).dialog("close");
                        }.bind(this));
                },
                'Cancel': function()
                {
                    $(this).dialog("close");
                }
            });

            dialog.dialog("open");
*/

        };

        this.invokeEditUserDialog = function(user)
        {
            var self = this;
            var dialog = $("#pg-admin-edit-user-dialog");

            self.editUser.id(user.id);
            self.editUser.name(user.name());
            self.editUser.password(user.password);


            $('#save-edit-user-dialog').bind('click', function(e) {
                e.preventDefault();

                self.api.changeUserName(self.editUser.id(), self.editUser.name())
                    .fail(function(message)
                    {
                        alert(message);
                    })
                    .done(_.bind(function(result)
                    {
                        user.name(result.username);
                    }, this));
                dialog.modal('hide');
            });

            dialog.modal('show');
            dialog.bind('hidden', function() {
                $('#save-edit-user-dialog').unbind('click');
                dialog.unbind('hidden');
            });
        };

        this.expandAllGrants = function()
        {
            for (var i = 0; i < this.users().length; i++)
            {
                this.users()[i].expandGrants();
            }
        };

        this.collapseAllGrants = function()
        {
            for (var i = 0; i < this.users().length; i++)
            {
                this.users()[i].collapseGrants();
            }
        };

        this.invokeAddUserDialog = function()
        {
            var self = this;
            var dialog = $("#pg-admin-create-user-dialog");

            self.newUser.id('');
            self.newUser.name('New user');
            self.newUser.password('');

            $('#save-create-user-dialog').bind('click', function(e) {
                e.preventDefault();
                self.api.addUser(self.newUser.id(), self.newUser.name(), self.newUser.password())
                    .fail(function(message)
                        {
                            alert(message);
                        })
                    .done(_.bind(function(result)
                        {
                            self.users.push(
                                new PhpGenAdmin.UserViewModel(
                                    self.api,
                                    result.id,
                                    result.name,
                                    result.password,
                                    true
                                ));
                            $(this).dialog("close");
                        }, this));

                dialog.modal('hide');
            });

            dialog.modal('show');

            dialog.bind('hidden', function() {
                $('#save-create-user-dialog').unbind('click');
                dialog.unbind('hidden');
            });


            //dialog.dialog("widget").find("div.ui-dialog-buttonpane").remove();



            /*dialog.modal("option", "buttons", {
                "Create user": function()
                {
                    self.api.addUser(self.newUser.id(), self.newUser.name(), self.newUser.password())
                        .fail(function(message)
                        {
                            alert(message);
                        })
                        .done(function(result)
                        {
                            self.users.push(
                                new PhpGenAdmin.UserViewModel(
                                    self.api,
                                    result.id,
                                    result.name,
                                    result.password,
                                    true
                                ));
                            $(this).dialog("close");
                        }.bind(this));
                },
                'Cancel': function()
                {
                    $(this).dialog("close");
                }
            });

            $("#pg-admin-create-user-dialog").dialog("open");*/
        };

        this.users = ko.observableArray([]);

        this.usersOnCurrentPage = ko.dependentObservable(function ()
        {
            return this.users.slice(0, this.users().length);
        }, this);
    };
})();

$(function()    {
    var api = new PhpGenAdmin.Api('phpgen_admin.php');
    PhpGenAdmin.adminPanelViewModel = new PhpGenAdmin.AdminPanelViewModel(api);

    var i;
    for(i = 0; i < PhpGenAdmin.CurrentUsers.length; i++)
        PhpGenAdmin.adminPanelViewModel.users.push(
            new PhpGenAdmin.UserViewModel(
                api,
                PhpGenAdmin.CurrentUsers[i].id,
                PhpGenAdmin.CurrentUsers[i].name,
                PhpGenAdmin.CurrentUsers[i].password,
                PhpGenAdmin.CurrentUsers[i].editable
            ));

    ko.applyBindings(PhpGenAdmin.adminPanelViewModel);

    $("#pg-admin-change-user-password-dialog").modal({show : false});
    $("#pg-admin-create-user-dialog").modal({show : false});
    $("#pg-admin-edit-user-dialog").modal({show : false});

});
