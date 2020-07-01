export default [
    {
        _name: 'CSidebarNav',
        _children: [
            {
                _name: 'CSidebarNavItem',
                name: '一般ユーザー',
                pathName: 'portal.users.search',
                icon: `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" viewBox="0 0 14 16">
                        <path id="Path_11" data-name="Path 11" d="M7,8A4,4,0,1,0,3,4,4,4,0,0,0,7,8ZM9.8,9H9.278A5.44,5.44,0,0,1,4.722,9H4.2A4.2,4.2,0,0,0,0,13.2v1.3A1.5,1.5,0,0,0,1.5,16h11A1.5,1.5,0,0,0,14,14.5V13.2A4.2,4.2,0,0,0,9.8,9Z" fill="#c2cedd"/>
                    </svg>`
            },
            {
                _name: 'CSidebarNavItem',
                name: '管理ユーザー',
                pathName: 'portal.company-admin-users.search',
                icon: `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" viewBox="0 0 14 16">
                            <path id="Path_22" data-name="Path 22" d="M13.625,15H13V.75A.75.75,0,0,0,12.25,0H1.75A.75.75,0,0,0,1,.75V15H.375A.375.375,0,0,0,0,15.375V16H14v-.625A.375.375,0,0,0,13.625,15ZM4,2.375A.375.375,0,0,1,4.375,2h1.25A.375.375,0,0,1,6,2.375v1.25A.375.375,0,0,1,5.625,4H4.375A.375.375,0,0,1,4,3.625Zm0,3A.375.375,0,0,1,4.375,5h1.25A.375.375,0,0,1,6,5.375v1.25A.375.375,0,0,1,5.625,7H4.375A.375.375,0,0,1,4,6.625ZM5.625,10H4.375A.375.375,0,0,1,4,9.625V8.375A.375.375,0,0,1,4.375,8h1.25A.375.375,0,0,1,6,8.375v1.25A.375.375,0,0,1,5.625,10ZM8,15H6V12.375A.375.375,0,0,1,6.375,12h1.25A.375.375,0,0,1,8,12.375Zm2-5.375A.375.375,0,0,1,9.625,10H8.375A.375.375,0,0,1,8,9.625V8.375A.375.375,0,0,1,8.375,8h1.25A.375.375,0,0,1,10,8.375Zm0-3A.375.375,0,0,1,9.625,7H8.375A.375.375,0,0,1,8,6.625V5.375A.375.375,0,0,1,8.375,5h1.25A.375.375,0,0,1,10,5.375Zm0-3A.375.375,0,0,1,9.625,4H8.375A.375.375,0,0,1,8,3.625V2.375A.375.375,0,0,1,8.375,2h1.25A.375.375,0,0,1,10,2.375Z" fill="#a9b7c9"/>
                        </svg>`
            },
            {
                _name: 'CSidebarNavItem',
                name: '名刺',
                pathName: 'portal.business-cards.search',
                icon: `<svg id="名刺" xmlns="http://www.w3.org/2000/svg" width="18" height="11" viewBox="0 0 18 11">
                        <g id="Rectangle_956" data-name="Rectangle 956" fill="none" stroke="#a9b7c9" stroke-width="1">
                        <rect width="18" height="11" rx="2" stroke="none"/>
                        <rect x="0.5" y="0.5" width="17" height="10" rx="1.5" fill="none"/>
                        </g>
                        <line id="Line_13" data-name="Line 13" x2="13" transform="translate(2.5 3.5)" fill="none" stroke="#a9b7c9" stroke-linecap="round" stroke-width="1"/>
                        <line id="Line_13-2" data-name="Line 13" x2="13" transform="translate(2.5 5.5)" fill="none" stroke="#a9b7c9" stroke-linecap="round" stroke-width="1"/>
                        <line id="Line_13-3" data-name="Line 13" x2="6" transform="translate(2.5 7.5)" fill="none" stroke="#a9b7c9" stroke-linecap="round" stroke-width="1"/>
                    </svg>`
            },
            {
                _name: 'CSidebarNavItem',
                name: '決済承認',
                pathName: 'portal.settlements',
                icon: `<svg xmlns="http://www.w3.org/2000/svg" width="16.001" height="16" viewBox="0 0 16.001 16">
                        <path id="Path_29" data-name="Path 29" d="M15.875,11.837l-.834-5A1,1,0,0,0,14.053,6H6.4V4h3a.5.5,0,0,0,.5-.5V.5A.5.5,0,0,0,9.4,0h-8A.5.5,0,0,0,.9.5v3a.5.5,0,0,0,.5.5h3V6H1.75a1,1,0,0,0-.987.834l-.834,5a1.939,1.939,0,0,0-.028.328V15a1,1,0,0,0,1,1h14a1,1,0,0,0,1-1V12.166A1.659,1.659,0,0,0,15.875,11.837ZM8.653,7.75a.5.5,0,0,1,.5-.5h.5a.5.5,0,0,1,.5.5v.5a.5.5,0,0,1-.5.5h-.5a.5.5,0,0,1-.5-.5Zm-1,2h.5a.5.5,0,0,1,.5.5v.5a.5.5,0,0,1-.5.5h-.5a.5.5,0,0,1-.5-.5v-.5A.5.5,0,0,1,7.653,9.75Zm-1-2.5a.5.5,0,0,1,.5.5v.5a.5.5,0,0,1-.5.5h-.5a.5.5,0,0,1-.5-.5v-.5a.5.5,0,0,1,.5-.5ZM2.4,2.5v-1h6v1Zm1.25,6.25h-.5a.5.5,0,0,1-.5-.5v-.5a.5.5,0,0,1,.5-.5h.5a.5.5,0,0,1,.5.5v.5A.5.5,0,0,1,3.653,8.75Zm.5,2v-.5a.5.5,0,0,1,.5-.5h.5a.5.5,0,0,1,.5.5v.5a.5.5,0,0,1-.5.5h-.5A.5.5,0,0,1,4.153,10.75Zm6.75,3.5a.251.251,0,0,1-.25.25h-5.5a.251.251,0,0,1-.25-.25v-.5a.251.251,0,0,1,.25-.25h5.5a.251.251,0,0,1,.25.25Zm.75-3.5a.5.5,0,0,1-.5.5h-.5a.5.5,0,0,1-.5-.5v-.5a.5.5,0,0,1,.5-.5h.5a.5.5,0,0,1,.5.5Zm1.5-2.5a.5.5,0,0,1-.5.5h-.5a.5.5,0,0,1-.5-.5v-.5a.5.5,0,0,1,.5-.5h.5a.5.5,0,0,1,.5.5Z" transform="translate(0.1)" fill="#a9b7c9"/>
                    </svg>`
            },
        ]
    }
];
