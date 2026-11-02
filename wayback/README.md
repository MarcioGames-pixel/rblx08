# Wayback originals

This folder stores the reproducible import workflow for the original Roblox archive captures.

Run Actions > Import original Wayback captures > Run workflow. The workflow downloads the 11 dated pages and the captured CSS/image/script resources that Wayback still serves, then publishes rblx08-wayback-originals.zip as an Actions artifact.

The page captures use the dates recorded in the supplied Roblox2008Pages.zip. Some captures or assets can be missing from the Wayback archive; the importer reports failures instead of fabricating replacements.

These files are reference material. They are not blindly included in the live PHP output because archived markup contains historical links, old scripts, and Wayback replay wrappers. The next integration step is to map each page's layout and original CSS into the corresponding PHP route while keeping the current account/database logic.
