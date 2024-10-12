<?php
    if(Database::getInstance()) {
        Database::getInstance()->closeConnection();
    }
