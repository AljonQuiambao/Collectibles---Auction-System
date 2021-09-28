CREATE TABLE users (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,
	address VARCHAR(255) NOT NULL,
	username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
	date_of_birth DATETIME,
	gender VARCHAR(255) NOT NULL,
	contact VARCHAR(255),
	role VARCHAR(255) NOT NULL, 
    subscription VARCHAR(255),
	payment_option VARCHAR(255),
	date_added DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users VALUES (4, "Administrator", "admin address", "admin", "admin", NOW(), "Male", 12345678901, 3, NULL, NULL, NOW());


CREATE TABLE items (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	title VARCHAR(255) NOT NULL,
	category VARCHAR(255) NOT NULL,
	details VARCHAR(1000) NOT NULL,
	item_images BLOB,
	token DECIMAL(10,2) NOT NULL,
	status TINYINT(4),
	is_deleted BOOLEAN DEFAULT FALSE,
	bid_time DATETIME DEFAULT CURRENT_TIMESTAMP,
	date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
	date_updated DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO items VALUES (1, "Red Can", "Albums", "Red was an album of heartbreak and healing, of rage and rawness, of 
tragedy and trauma, and of .he loss of an imagined future alongside someone. I wrote Ronan while I was making Red and 
discovered your story as you so honestly and devastatingly told it.", 2000, NULL, 1000, 2, false, NOW(), NOW(), NOW());

CREATE TABLE item_status (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	item_id INT(11) NOT NULL,
	category INT(11) NOT NULL,
	status TINYINT(4),
	reason VARCHAR(2000)
);

INSERT INTO item_status VALUES (1, 6, 1, 2, 1, "");

CREATE TABLE item_category (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	category_id INT NOT NULL,
	category VARCHAR(50) NOT NULL
);

INSERT INTO item_category VALUES (1, 1, "Albums");
INSERT INTO item_category VALUES (2, 2, "Coins");
INSERT INTO item_category VALUES (3, 3, "Paintings");
INSERT INTO item_category VALUES (4, 4, "Sports Related");
INSERT INTO item_category VALUES (5, 5, "Toys");        

CREATE TABLE tokens (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	token DECIMAL(10,2) NOT NULL
);

INSERT INTO tokens VALUES (1, 1, 2000.00);


CREATE TABLE comments (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	item_id INT(11) NOT NULL,
	comment VARCHAR(2000),
	date_posted DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO comments VALUES (1, 1, 1, "This is a sample comments for the above item.", NOW());

CREATE TABLE notifications (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	item_id INT(11) NOT NULL,
	type TINYINT(4),
	notification VARCHAR(2000),
	status TINYINT(4),
	date_posted DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO notifications VALUES (1, 6, 1, 0, "This is a sample notification for the above item.", 0 , NOW());

CREATE TABLE messages (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	type TINYINT(4),
	message VARCHAR(2000),
	status TINYINT(4),
	date_message DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- For Bidding
CREATE TABLE bidding_sessions (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	item_id INT(11) NOT NULL,
	auctioneer_id INT(11) NOT NULL,
	bidding_time DATETIME DEFAULT CURRENT_TIMESTAMP,
	current_bid DECIMAL(20,0) NOT NULL
);

CREATE TABLE bidding_history (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	bidding_session_id INT(11) NOT NULL,
	item_id INT(11) NOT NULL,
	auctioneer_id INT(11) NOT NULL,
	bidder_id INT(11) NOT NULL,
	bid_token DECIMAL(10,2) NOT NULL
);

CREATE TABLE bid_items (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	auctioneer_id INT(11) NOT NULL,
	bidder_id INT(11) NOT NULL,
	item_id INT(11) NOT NULL,
	status TINYINT(4),
	date_bid_end DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO bid_items VALUES (1, 1, 1, 2);

CREATE TABLE item_proof (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	auctioneer_id INT(11) NOT NULL,
	bidder_id INT(11) NOT NULL,
	item_id INT(11) NOT NULL,
	proof longblob NOT NULL,
	date_received DATETIME DEFAULT CURRENT_TIMESTAMP,
	date_submit DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE item_images (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	auctioneer_id INT(11) NOT NULL,
	item_id INT(11) NOT NULL,
	image longblob NOT NULL,
	date_uploaded DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE item_status_enum (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	item_status_enum_id INT(11) NOT NULL,
	status TINYINT(4) 
);

INSERT INTO item_status_enum VALUES (1, 1, "Pending");
INSERT INTO item_status_enum VALUES (2, 2, "Approved");
INSERT INTO item_status_enum VALUES (3, 3, "Rejected");
INSERT INTO item_status_enum VALUES (4, 4, "Ready for Bid");
INSERT INTO item_status_enum VALUES (5, 5, "Sold");

CREATE TABLE messages (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  convo_id int(30) NOT NULL,
  user_id int(30) NOT NULL,
  message text NOT NULL,
  status tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=unread , 1= read',
  date_created datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
);

INSERT INTO messages VALUES (1, 1, 2, 'hi', 1, '2020-10-13 21:03:22');

CREATE TABLE thread (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  user_ids text NOT NULL
);

INSERT INTO thread VALUES (1, '1, 2');

CREATE TABLE feedbacks (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	feedback VARCHAR(255),
	comment text NOT NULL,
	date_created datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
);