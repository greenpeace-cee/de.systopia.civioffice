# Working With Documents

## Known issues

+ .docx files use an XML structure internally to describe formatting and other
  properties of text elements. In some cases, this can lead to CiviCRM tokens
  being split up by XML tags, making them infunctional. CiviOffice tries to
  mitigate this problem by optimizing the XML structure. However, there are some
  things that CiviOffice can not repair (yet). **In order to avoid issues, you
  should**

  + make sure there are no mixed formatting properties in your tokens (e.g. a
    portion of the token is in bold font)

  + disable spell checking in your documents, especially make sure you don't
    apply exceptions from spell checking to bits of your tokens

+ It is also advisable not to use "exotic" image file types in your docs - the
  most common ones should work though.

+ Smarty syntax is not supported in CiviOffice templates.

## Managing Document Templates

With the integrated document stores for personal and shared uploads, the
documents to be used as templates in CiviOffice can be managed
via `/civicrm/civioffice/document_upload`. (During installation, a navigation
link to this page should have been created.)

The local document store can distinguish between private Documents (only
available to the user who uploaded them) and shared documents (available to all
users). Use the tabs to switch between both. With the appropriate permissions,
you can upload, download and delete documents (currently, only .docx format is
supported).

In future versions, other document stores might be added. This could also be
remote document servers such as NextCloud. Currently however, only the local
document store is available - funding for further developement is welcome.

## Using tokens to insert data into your templates

You can use CiviCRM tokens in your document templates. A page listing all the
tokens available in your system can be found at `/civicrm/civioffice/tokens`. (
During installation, a navigation link to this page should have been created.)

Note that it depends on the context whether or not a specific set of tokens is
actually functional! For example, contribution tokens will *not* work when
operating on contacts (i.e. creating a document for a single contact, or many
documents from a contact search result). Contribution tokens will only be
populated with data when you are operating on contributions, e.g. after a
contribution search.

## Creating a document for a single contact

In the overview for a single contact, the extension creates a new option in
the `Actions` menu, `Create CiviOffice document`. Clicking this menu item will
open a form where you choose some settings for your document processing:

- pick a document to be used as the template, a renderer to process it and the
  output format
- ~~'Prepare DOCX documents'~~ this option has been moved to global
  configuration, TODO: updated screenshot image
- choose whether you want to create an activity, and if so, whether to include
  the rendered document
- if there are any live snippets configured in your system, you can fill them
  here (read more about live
  snippets [here](inserting-content-with-live-snippets.md))

![CiviOffice generate single document](../img/civioffice-generate-single-document.png "CiviOffice generate documents")

**Download document** will do just that and, if applicable, create an activity.
**Preview** will just give you the document to review, but not create an
activity.

## Creating documents for multiple contacts

![CiviOffice generate documents](../img/civioffice-generate-documents.png "CiviOffice generate documents")

From a contact search result, select the intended contact records. Then, select
the action **Create Documents** which will take you to similar form with
processing options. This is different from the form for a single contact:

- you can change the batch size for processing documents (leave unchanged unless
  you have issues with the processing speed)
- attaching files to activities is not supported (yet) as this can produce large
  amounts of data and thus harm the application by using up the hard drive space
- the download will produce a .zip archive containing the document for each
  contact

**After downloading, always use the 'Back to previous page' button in order to
delete the created document from the main memory and thus free up the memory
space. This is particularly important for larger processing runs.**

## Creating documents from SearchKit

It is also possible to create documents for entities in SearchKit results. The
procedure is nearly identical to the one described above. From the search result
select the entities you want to create documents for. Then choose the action
**Create Documents (CiviOffice)** from the **Action** menu. It opens a dialog
where you can select the document template to use and change other options, if
necessary. Then click the **Create Documents** button. A new browser tab opens
and when the rendering is finished you'll be prompted to download the file.
Afterward the browser tab can be closed. Before the tab opens your browser might
ask to open a new tab what you need to allow.

![CiviOffice Creating documents from SearchKit](../img/searchkit-create-document.png "CiviOffice SearchKit action")

## Creating documents for activities, cases, contributions, events, memberships, and participants

CiviOffice also provides search actions in activity, case, contribution, events,
membership, and participant search results. The processing options are similar
as in the contact search action, however the data available through tokens will
be different. For example, you can use a token for the contribution amount when
operating on contribution search results, or for the event title belonging to a
registration when operating on participant search results, respectively.