=== CAPTCHA in Thai 2nd ===
Contributors: Thanate, nattapon_wora, Kunanon, K.Pipatjessadakul
Donate link: http://www.captcha.in.th/
Tags: captcha, thai, spam, comment
Requires at least: 3.3
Tested up to: 3.8.1
Stable tag: 1.0 Beta
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== description ==

CAPTCHA in Thai เป็นการบริการฟรี CAPTCHA ที่เป็นภาษาไทย ในลักษณะของการแก้ไขคำที่มักเขียนผิด คำอ่าน สุภาษิต และคำราชาศัพท์ต่างๆ
เพื่อช่วยป้องกันเว็บไซด์ของท่านจากสแปม ท่านสามารถติดตั้งใช้ได้หลายส่วน ได้แก่ การเข้าสู่ระบบ, การลงทะเบียนและการแสดงความคิดเห็น 
นอกจากนี้การใช้ CAPTCHA in Thai เป็นการส่งเสริมให้ผู้ใช้งานสามารถใช้ภาษาไทยได้ถูกต้องอีกด้วย

CAPTCHA in Thai is a free CAPTCHA service in Thai version. The pattern will be in the forms 
of the correction of the words which usually wrong-written, words’ pronunciation and many 
different adages. By doing this, the website will be protected from spamming. CAPTCHA in Thai can 
be installed in different functions which are log-in, registration and comment function. Moreover,
using CAPTCHA in Thai can be useful in terms of using correct form of Thai language.

= การทำงาน =
1. เมื่อผู้ใช้เข้าหน้าที่ถูกตั้งค่าให้ใช้งาน captcha จะมีการร้องขอภาพคำถามจากผู้ติดตั้งปลั๊กอิน โดยมีการส่ง ip address 
ของผู้ที่ต้องตอบ captcha เพื่อใช้ในการเก็บสถิติไปด้วย
2. เมื่อผู้ใช้ ทำการกรอกข้อมูลในหน้านั้นๆ พร้อมกับคำตอบของ captcha ปลั๊กอินจะส่งคำตอบกลับไปตรวจสอบที่เซิฟเวอร์ของผู้ติดตั้งปลั๊กอิน
พร้อม ip address ของผู้ตอบ จากนั้นเซิฟเวอร์ของผู้ติดตั้งจะทำการส่งข้อมูลว่าถูกหรือผิดกลับมา

= ปลั๊กอินนี้จะช่วยอะไรคุณได้บ้าง: =

* ช่วยให้เว็บของคุณมีการป้องกันสแปมผ่าน CAPTCHA ในรูปแบบภาษาไทย
* สามารถเลือกการใช้บริการได้หลากหลายรูปแบบ ได้แก่ คำที่มักเขียนผิด คำอ่าน สุภาษิต และคำราชาศัพท์ และยังเรียกใช้ได้หลายส่วนการทำงาน
* สามารถเลือกสีของตัวอักษร, สีของพื้นหลัง, รูปแบบของตัวอักษร, รูปแบบฟอนต์ และยังสามารถสุ่มสี สุ่มตัวอักษรได้
* สามารถเลือกเลือกระดับของการป้องกันได้

= What do CAPTCHA in Thai help? =

* Protect your websites from spam
* Many features available including wrong-written, words’ pronunciation, many different adages and royal language.
* Adjustable word’s color, background ’s color, Font and you can choose random  word’s color, background ’s color, Font
* Adjustable level of security
* Supporting offline mode when unable to connect to CAPTCHA in Thai Server

== Installation ==

1. ติดตั้ง CAPTCHA in Thai ลงในโฟล์เดอร์ /wp-content/plugins/
2. คลิกที่ Active ของปลั๊กอิน CAPTCHA in Thai ในเมนูของปลั๊กอินใน Wordpress
3. คลิก Settings ในปลั๊กอิน CAPTCHA เพื่อเลือกรูปแบบการทำงาน  

1. Upload `CAPTCHA in Thai` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Plugin settings are located in 'Settings', 'CAPTCHA in Thai' 

== Requriement ==

* ต้องใช้ wordPress เวอร์ชั่น 3.0 ขึ้นไป

== Frequently Asked Questions ==

= CAPTCHA คืออะไร? =

CAPTCHA (Completely Automated Public Turing Test To Tell Computers and Humans Apart) 
คือ ระบบที่ใช้ทดสอบเพื่อจำแนกความแตกต่างระหว่างมนุษย์และคอมพิวเตอร์ได้อย่างสมบูรณ์ โดยสร้างการทดสอบที่มนุษย์สามารถแก้ปัญหาได้อย่างถูกต้อง 
แต่คอมพิวเตอร์ในปัจจุบันไม่สามารถแก้ปัญหานี้ได้

= CAPTCHA มีประโยชน์อย่างไร? =

CAPCHA จะช่วยป้องกันการสมัครการใช้งานต่างๆของเว็บไซต์และป้องกันสแปม จากโปรแกรมอัตโนมัติ (bots) โดยการนำ CAPTCHA มาใช้กับ
แบบฟอร์มการสมัครสมาชิกหรือแบบฟอร์มป้อนข้อความต่างๆ เพื่อเป็นการยืนยันว่า ผู้ที่กรอกแบบฟอร์มเป็นมนุษย์ เนื่องจากโปรแกรมอัตโนมัติเหล่านี้จะ
ไม่สามารถแก้ปัญหาจาก CAPTCHA ได้

= ทำไมต้องเป็นรูปแบบภาษาไทย? =

เนื่องจากในปัจจุบัน CAPTCHA ที่เป็นภาษาไทยยังมีจำนวนน้อย และไม่สะดวกในการนำมาใช้งาน จึงได้จัดทำ CAPTCHA ในรูปแบบภาษาไทยขึ้นมา
อีกทั้งยังเป็นรูปแบบการใช้คำที่มักเขียนผิดในภาษาไทยเพื่อเป็นการส่งเสริมให้ผู้ใช้งานสามารถใช้ภาษาไทยได้ถูกต้องมากขึ้นอีกด้วย

= What is CAPTCHA? =

CAPTCHA (Completely Automated Public Turing Test To Tell Computers and Humans Apart) 
is testing system for completely identify human out of computer by create the test that human can solve but not computer.

= What is the CAPTCHA's advantage? =

CAPTCHA can protect computer from bots, spams and auto signing up any program. User can use CAPTCHA with registered form or text form for insure that human do it because automatic program cann't solve CAPTCHA problem.

= Why should use Thai version? =

Because, Now there are a few Thai version CAPTCHA and the most of them aren't convenience for using. So we create New Thai version CAPTCHA. Moreover this new CAPTCHA plenty of Thai word that often wrong spell. The user must correct the word for access program.
Therefor this CAPTCHA will encourage right spell Thai word.

== Screenshots ==
1. หน้าการตั้งค่าปลั๊กอิน : screenshot-1.png : http://www.captcha.in.th/documents/screenshot-1.png
2. ตัวอย่าง captcha in Thai ในหน้าเข้าสู่ระบบ : screenshot-2.png : http://www.captcha.in.th/documents/screenshot-2.png
3. ตัวอย่าง captcha in Thai ในหน้าลืมพาสเวิด : screenshot-3.png : http://www.captcha.in.th/documents/screenshot-3.png
4. ตัวอย่าง captcha in Thai ในหน้าสมัครสมาชิก : screenshot-4.png : http://www.captcha.in.th/documents/screenshot-4.png

== Changelog ==

= 1.0 Beta =
* เวอร์ชั่นเริ่มต้นของ CAPTCHA in Thai 2nd

= 1.0 Beta =
* Initial CAPTCHA in Thai 2nd version
