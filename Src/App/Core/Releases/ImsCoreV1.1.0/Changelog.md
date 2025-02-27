### IMS Core v1.1.0
#### 2021-09-21

<br />
<br />

#### Additions
- Add some items to left side bar.
- Release first version of release/update core.


<br />

#### Changes
- Change all ctype references from (by id) to (by name).
- Change ctypes references from id to name
    - AddEmail()
    - AddNotification()
    - AddSms()
- Add token field to FieldCollections
- Change relation_1_id to parent_id in subtables.
- Change relation_2_id to value_id in subtables.
- Fix attachment field orgin_name to origin_name.
- Remove created_user, updated_user, created_date, updated_date for FieldCollections.
- Trim name and title of ctypes
- change SYSTEM_USER_ID and GUEST_USER_ID to get to get it from db
