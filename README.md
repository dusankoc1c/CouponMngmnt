
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
 
