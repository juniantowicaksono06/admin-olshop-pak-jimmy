import * as ajaxModule from './ajax-module.js';
import * as commandModule from './command-module.js';
import * as confirmationDialogModule from './confirmation-dialog-module.js';

export const app_ajaxModule = ajaxModule;

export const app_commandModule = commandModule;

export const app_confirmationDialogModule = confirmationDialogModule;

export const app_confirmationDialog = app_confirmationDialogModule.ConfirmationDialog;