
## 14/15. Septembar

- Home page koji vodi ka registraciji i loginu, stranice login i registracija napravljene
- Napravljeni modeli i migracije za User, Store, Bundle, Coupon
- Povezano sve sa bazom
- Urađena registracija, prijava i logout
- Dashboard, dodavanje jednog store-a
- Dodavanje bundle-a i dodavanje kupona
- Generisanje koda kupona iz 2 slova imena bundle-a
- Dodavanje novih kupona u bundle
- Edit kupona

## 16. Septembar

 - EmailTrap config
 - Slanje maila, ako nema datuma, ako ima salje se na taj datum
 - Slanje reminder maila sa linkom za deaktivaciju subscribe-a
 - Slanje vise kupona na mail ako se kreiraju pri kreiranju bundl-a, al za malo (FIX)

## 17. Septembar
- popravljenje rute za backlink (valjda)
- dodavanje vise kupona od jednom kada se kreira bundle (bez receivera)
- Superadmin edit podataka drugih admina
- Refresh filtera za CSv export
- Export svih prodavnica od jednom za Superadmina
- Uvedene Request klase za validaciju 
- Service-Repo refaktor (Store, Bundle, Coupon)
- Helper klase (Coupon code generator, CSV export)
- Soft delete 

## 18. Septembar

 - Indikatori kada je poslednji put poslat mejl ili reminder (kolona u kuponu)
 - Repo - Service (refaktor kompletan Admin)
 - Import kodova u app preko CSV za svaki bundle zasebno (ako se obrisu kuponi i onda se importuju opet isti ne radi FIXXXXX)
 - Svaki store ima setovan limit za ukupnu vrednost kupona iz svih bundlova ako je ta vrednost postavljena, onda Admini ne smeju da prekorace tu vrednost i dobijaju gresku prilikom kreiranja deck-a da bi prekoracili. Vrednost setuju superadmini prilikom slanja invite-a:
 	    1. Migracije u bazi za dodavanje value na Store, User, Invite
 	    2. Invite limit -> User store limit -> Store limit
 	    3. StoreService metoda za proveru limita (CoponService i BundleService ga injektuju)
 	    4. Mesta provere : kreiranje bundla, dodavanje pojedincanog kupona, update kupona, csv import 
 - Redovno proveravanje da li je kod expired i menjanje statusa, dodato Expires_at kolona kuponima (ako nema expires at za kupone, uzima expires at od bundl-a)


## 21. Septembar

 - Slanje vise mejlovo od jednom iz bundla reseno (prelazak na novog providera, MAIL PIT lokalno)
 - Dodati za svaki bundle i coupon postoje opcije ponovnog rucnog slanja maila i inicijalni i reminder (klikom na resend bundle sve se salje)
 - Dodati templejte za mejlove. Svaki deck dolazi da nekim default sadrzajem za mejl. Admini mogu da menjaju taj template 
 - Laravel Spite (security) (zamenjene role/permisije OSTAJE security headers i rate limiting)

## 22. Septembar

- kada stoji prazan send date da se ne salje nego da uvedemo flag (checkbox) send now
  - polje na koliko dana se salje reminder (umesto fiksnih 20)
  - trajno resenje za import CSV fajlova 
  - Import kodova u app preko CSV ne radi ako se obrisu kuponi i onda se importuju opet isti (ako se Kod Kupona poklapa sa nekim kodkom koji je vec u app mora da se ispise poruka)
  	1. kada se kod iz CSV poklopi sa postojecim (soft deleted), ako se sva polja poklapaju restore() iz baze, ako je aktivan vec (postoji u sistemu) preskace se
  	2. ako se jedan red (kupon) poklapa a ostala polja se ne poklapaju, izaziva se konflikt (ceka import ceo CSV fajl isto kao za value limit)
  	3. import svezih baza prazna - radi
        4. import, brisanje par kupona, import cele liste, vrate se oni koji su obrisani - radi
        5. export, delete bundla, import celog bundla - radi 
IMPORT TEST CASES : ////////////////////////////////////
	1. export, obrisan bundle, import exportovanih u novi bundle
	2. iz bundle obrisan kupon, import CELOG CSV fajla - vraca se kupon u bundle 
	3. ako kupon ima isti kod, i pokusa da se importuje opet a NEMA IDENTICNE podatke blokira se 
	4. value limit prekoracen, blokira se ceo import 
	5. ako nema polje expires_at kupon, override-uje se iz expires_at od bundla 
	6. ako drugi user pokusa da importuje iste kupone kao neki user koji ih vec koristi, blokira se import
	7. ako jedan user obrise bundle sa kuponima, drugi user moze da importuje kupone AKO SU IDENTICNI sa tim obrisanim
	
    - Spatie Sec Headers :
    	1. ne moze addEventListener jer se tier redovi prave dinamicki preko js
    	2. event delegation data-* (jedan listener za ceo blade)
    	3. otvaranje modala
    	4. zatvaranje modala
    	5. dropdown meni
    	6. dodavanje tiera
    	7.brisanje filtera
    	8. racunanje tier totala
    - rate limiting



## 23. Septembar
- Sanctum Personal Acces Token (zato sto ce frontend da bude na drugom serveru)
- Auth point (login/logout) 
- CRUD Store  
- CRUD Bundle  
- CRUD Coupon  
- Send Mail (initial, reminder, send all) 
- Update Email Template 
- Admin/Invite 
- CSV impoprt/export 
- Register 
- Coupon toogle-ussed/unsubscribe 
- Coupon Mass Add
- Edge case import 
- Dokumentacija
 
