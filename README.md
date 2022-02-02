<h1>Twitch Test</h1>

The assignment done from my understanding of what is asked.

Overview

<ul>
    <li>Install Laravel
        <ul>
            <li>Run composer</li>
            <li>Run artisan migrate</li>
        </ul>
    </li>
    <li>User Account            
        <ul>
            <li>Laravel Breeze</li>
            <li>Laravel Passport</li>
            <li>Create Account</li>
        </ul>
    </li>
    <li>OAuth
        <ul>
            <li>Twitch Access            
                <ul>
                    <li>Authorize Twitch</li>
                    <li>OAuthController::authorizeAccess()</li>
                    <li>OAuthController::access()</li>
                    <li>model Authentication</li>
                </ul>
            </li>
            <li>Access Tokens</li>
            <li>Refresh Tokens</li>
        </ul>
    </li>
    <li>User Data
        <ul>
            <li>Twitch ID</li>
            <li>Twitch Login</li>
            <li>Follows</li>
            <li>Tags</li>
        </ul>
    </li>
    <li>Cron Jobs
        <ul>
            <li>Laravel Scheduler | * * * * * cd /var/www/html/www.twitchtest.com && php artisan schedule:run >> /dev/null 2>&1</li>
            <li>Pull Streams (every 15min)</li>
            <li>Refreshing Tokens (every 5min)</li>
        </ul>
    </li>
    <li>Backend Queries
        <ul>
            <li>Assignment Requests            
                <ul>
                    <li>model TwitchStreams</li>
                    <li>model Twitch</li>
                    <li>model TwitchStreams</li>
                </ul>
            </li>
        </ul>
    </li>
    <li>Front End    
        <ul>
            <li>Assignment Requests        
                <ul>
                    <li>HomeController::index()</li>
                    <li>Bootstrap</li>
                    <li>Top 1000</li>
                    <li>Top 100</li>
                    <li>Streams group by Start Hour</li>
                    <li>Shared Tags from Followers</li>
                    <li>Shared Tags from Top 1000</li>
                </ul>
            </li> 
        </ul>
    </li>
</ul>
