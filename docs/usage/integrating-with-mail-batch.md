# Sending E-Mail with personalized attachments

You can enhance CiviOffice's functionality with some additional extensions in
order to send out personalized e-mail attachments to your contacts. A use case
for this might be sending certificates of attendance to your event participants
attached to an email. However, you can also create personalized attachments
based on contribution data or just plain contacts.

The additional tools you will need to install are:

- de.systopia.mailattachment 1.0 (or higher)
- de.systopia.mailbatch-2.0 (or higher), this provides a search action for
  contacts and contributions
- de.systopia.eventmessages-1.1 (or higher), this provides a search action for
  participants and rule-based, automatic emails to participants

CiviOffice and the additional extensions will work without further
configuration (when installed in sufficient versions).

To use the feature with regard to contacts, contributions, or memberships,
search for contacts (or contributions, or memberships) and select the search
action 'Send Emails (via MailBatch)'. In the options interface, under
'Attachments', select 'CiviOffice Document' and choose the template you want to
use for your attachments. Any valid tokens will be replaced in the usual way.

To create personalized email attachments for event participants, perform a
participant search and use the search action 'Send E-Mails via EventMessages'.
Note that in this case, the From, Cc, Bcc and Reply-To settings are defined in
the event settings!

The *Custom Event Communication* extension also allows for automatic, rule-based
email communication to event participants (e.g. whenever a participant
registration changes from status pending to confirmed, send out this email ...).
CiviOffice-generated personalized attachments can also be attached to those
automatic emails!

**Please note:** There is no MailBatch integration for CiviCase yet, so you can
only create pdfs for cases so far.

This functionality is not available for SearchKit results.
