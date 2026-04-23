<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Converts a 978-prefixed ISBN-13 to ISBN-10.
     * Returns null for non-978 or non-13-digit strings.
     */
    private function toIsbn10(string $isbn13): ?string
    {
        $d = preg_replace('/\D/', '', $isbn13);
        if (strlen($d) !== 13 || substr($d, 0, 3) !== '978') {
            return null;
        }
        $body = substr($d, 3, 9);
        $sum  = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (10 - $i) * (int) $body[$i];
        }
        $check = (11 - ($sum % 11)) % 11;
        return $body . ($check === 10 ? 'X' : (string) $check);
    }

    public function run(): void
    {
        // ── Users (all 4 RBAC roles) ──────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@library.com'],
            ['name' => 'Admin', 'password' => Hash::make('password123'), 'role' => 'admin']
        );

        User::firstOrCreate(
            ['email' => 'staff@library.com'],
            ['name' => 'Staff Member', 'password' => Hash::make('password123'), 'role' => 'staff']
        );

        User::firstOrCreate(
            ['email' => 'subscriber@library.com'],
            [
                'name'                    => 'Subscribed User',
                'password'                => Hash::make('password123'),
                'role'                    => 'subscribed_user',
                'is_subscribed'           => true,
                'subscription_expires_at' => now()->addYear(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@library.com'],
            ['name' => 'Regular User', 'password' => Hash::make('password123'), 'role' => 'user']
        );

        // ── Books ─────────────────────────────────────────────────────────────
        $books = [
            // Programming
            ['title' => 'Clean Code',                              'author' => 'Robert C. Martin',          'isbn_13' => '9780132350884', 'genre' => 'Programming',  'book_type' => 'book', 'published_year' => 2008, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'A practical guide to writing readable, maintainable code. Covers naming, functions, comments, formatting, and refactoring.'],
            ['title' => 'The Pragmatic Programmer',                'author' => 'Andrew Hunt & David Thomas', 'isbn_13' => '9780201616224', 'genre' => 'Programming',  'book_type' => 'book', 'published_year' => 1999, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'Timeless advice for software developers on craftsmanship, career growth, and building better software systems.'],
            ['title' => 'Design Patterns',                        'author' => 'Gang of Four',               'isbn_13' => '9780201633610', 'genre' => 'Programming',  'book_type' => 'book', 'published_year' => 1994, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'The classic book on 23 reusable object-oriented software design patterns every developer should know.'],
            ['title' => 'Introduction to Algorithms',             'author' => 'Thomas H. Cormen',           'isbn_13' => '9780262033848', 'genre' => 'Programming',  'book_type' => 'book', 'published_year' => 2009, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'Comprehensive textbook on algorithms and data structures, the go-to reference for CS students and professionals.'],
            ['title' => "You Don't Know JS",                      'author' => 'Kyle Simpson',               'isbn_13' => '9781491904244', 'genre' => 'Programming',  'book_type' => 'book', 'published_year' => 2015, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'A deep dive into the core mechanisms of JavaScript — closures, prototypes, asynchrony, and more.'],
            ['title' => 'The Mythical Man-Month',                 'author' => 'Frederick P. Brooks Jr.',    'isbn_13' => '9780201835953', 'genre' => 'Programming',  'book_type' => 'book', 'published_year' => 1995, 'total_copies' => 2, 'available_copies' => 2, 'description' => "Essays on software engineering and project management, famous for Brooks's Law about adding people to late projects."],
            ['title' => 'Refactoring',                            'author' => 'Martin Fowler',              'isbn_13' => '9780201485677', 'genre' => 'Programming',  'book_type' => 'book', 'published_year' => 1999, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'A guide to improving the design of existing code through disciplined refactoring techniques with examples.'],

            // Self-Help
            ['title' => 'Atomic Habits',                          'author' => 'James Clear',                'isbn_13' => '9780735211292', 'genre' => 'Self-Help',    'book_type' => 'book', 'published_year' => 2018, 'total_copies' => 4, 'available_copies' => 4, 'description' => 'Proven framework for building good habits and breaking bad ones through tiny, incremental changes over time.'],
            ['title' => 'Deep Work',                              'author' => 'Cal Newport',                'isbn_13' => '9781455586691', 'genre' => 'Self-Help',    'book_type' => 'book', 'published_year' => 2016, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'Rules for focused success in a distracted world. Argues that the ability to perform deep work is increasingly rare and valuable.'],
            ['title' => 'The 7 Habits of Highly Effective People','author' => 'Stephen R. Covey',           'isbn_13' => '9780743269513', 'genre' => 'Self-Help',    'book_type' => 'book', 'published_year' => 1989, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'Timeless principles for personal and professional effectiveness based on character ethics and universal principles.'],
            ['title' => 'Thinking, Fast and Slow',                'author' => 'Daniel Kahneman',            'isbn_13' => '9780374533557', 'genre' => 'Self-Help',    'book_type' => 'book', 'published_year' => 2011, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'Nobel laureate Kahneman explores the two systems that drive our thinking — intuitive and deliberate — and their biases.'],
            ['title' => "Man's Search for Meaning",               'author' => 'Viktor E. Frankl',           'isbn_13' => '9780807014271', 'genre' => 'Self-Help',    'book_type' => 'book', 'published_year' => 1946, 'total_copies' => 3, 'available_copies' => 3, 'description' => "A Holocaust survivor and psychiatrist's memoir and theory of logotherapy, arguing that meaning is the primary human motivation."],
            ['title' => 'The Power of Now',                       'author' => 'Eckhart Tolle',              'isbn_13' => '9781577314806', 'genre' => 'Self-Help',    'book_type' => 'book', 'published_year' => 1997, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'A guide to spiritual enlightenment through present-moment awareness and releasing attachment to the thinking mind.'],

            // Science
            ['title' => 'A Brief History of Time',                'author' => 'Stephen Hawking',            'isbn_13' => '9780553380163', 'genre' => 'Science',      'book_type' => 'book', 'published_year' => 1988, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'From the Big Bang to black holes, an accessible exploration of cosmology and the nature of space and time.'],
            ['title' => 'Sapiens',                                'author' => 'Yuval Noah Harari',          'isbn_13' => '9780062316097', 'genre' => 'Science',      'book_type' => 'book', 'published_year' => 2011, 'total_copies' => 4, 'available_copies' => 4, 'description' => 'A sweeping narrative of human history — how Homo sapiens came to dominate the planet through cognition and cooperation.'],
            ['title' => 'The Selfish Gene',                       'author' => 'Richard Dawkins',            'isbn_13' => '9780198788607', 'genre' => 'Science',      'book_type' => 'book', 'published_year' => 1976, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'A landmark work arguing that natural selection is best understood from the gene-centered point of view.'],
            ['title' => 'The Body: A Guide for Occupants',        'author' => 'Bill Bryson',                'isbn_13' => '9780385539302', 'genre' => 'Science',      'book_type' => 'book', 'published_year' => 2019, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'A funny, informative journey through the human body — what it does, how it works, and how to keep it healthy.'],
            ['title' => 'The Gene: An Intimate History',          'author' => 'Siddhartha Mukherjee',       'isbn_13' => '9781476733500', 'genre' => 'Science',      'book_type' => 'book', 'published_year' => 2016, 'total_copies' => 2, 'available_copies' => 2, 'description' => "An epic history of genetics from Mendel's peas to CRISPR and the ethical questions it raises for humanity."],
            ['title' => 'Cosmos',                                 'author' => 'Carl Sagan',                 'isbn_13' => '9780345539434', 'genre' => 'Science',      'book_type' => 'book', 'published_year' => 1980, 'total_copies' => 2, 'available_copies' => 2, 'description' => "A personal voyage through the universe, blending astronomy, history, and philosophy in Sagan's signature style."],

            // Fiction
            ['title' => '1984',                                   'author' => 'George Orwell',              'isbn_13' => '9780451524935', 'genre' => 'Fiction',      'book_type' => 'book', 'published_year' => 1949, 'total_copies' => 4, 'available_copies' => 4, 'description' => 'A chilling dystopian novel about totalitarianism, surveillance, and the destruction of truth in Airstrip One.'],
            ['title' => 'To Kill a Mockingbird',                  'author' => 'Harper Lee',                 'isbn_13' => '9780061935466', 'genre' => 'Fiction',      'book_type' => 'book', 'published_year' => 1960, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'Pulitzer Prize-winning story of racial injustice and childhood innocence narrated by Scout Finch in the American South.'],
            ['title' => 'The Alchemist',                          'author' => 'Paulo Coelho',               'isbn_13' => '9780062315007', 'genre' => 'Fiction',      'book_type' => 'book', 'published_year' => 1988, 'total_copies' => 3, 'available_copies' => 3, 'description' => "A philosophical novel following a shepherd boy's journey across Egypt in pursuit of his personal legend."],
            ['title' => 'Dune',                                   'author' => 'Frank Herbert',              'isbn_13' => '9780441013593', 'genre' => 'Fiction',      'book_type' => 'book', 'published_year' => 1965, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'Epic sci-fi saga set on the desert planet Arrakis, blending politics, religion, and ecology into a sprawling epic.'],
            ['title' => 'The Great Gatsby',                       'author' => 'F. Scott Fitzgerald',        'isbn_13' => '9780743273565', 'genre' => 'Fiction',      'book_type' => 'book', 'published_year' => 1925, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'A critique of the American Dream told through the doomed obsession of Jay Gatsby in Jazz Age Long Island.'],
            ['title' => 'Pride and Prejudice',                    'author' => 'Jane Austen',                'isbn_13' => '9780141439518', 'genre' => 'Fiction',      'book_type' => 'book', 'published_year' => 1813, 'total_copies' => 3, 'available_copies' => 3, 'description' => "A witty, beloved novel about manners, marriage, and the spirited Elizabeth Bennet's romance with Mr. Darcy."],
            ['title' => 'One Hundred Years of Solitude',          'author' => 'Gabriel García Márquez',     'isbn_13' => '9780060883287', 'genre' => 'Fiction',      'book_type' => 'book', 'published_year' => 1967, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'Magical realist saga tracing seven generations of the Buendía family in the fictional Colombian town of Macondo.'],
            ['title' => 'Brave New World',                        'author' => 'Aldous Huxley',              'isbn_13' => '9780060850524', 'genre' => 'Fiction',      'book_type' => 'book', 'published_year' => 1932, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'A dystopian vision of a future society engineered for happiness through conditioning, consumerism, and technology.'],
            ['title' => 'The Road',                               'author' => 'Cormac McCarthy',            'isbn_13' => '9780307387899', 'genre' => 'Fiction',      'book_type' => 'book', 'published_year' => 2006, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'Pulitzer Prize-winning post-apocalyptic novel of a father and son walking toward the coast in a dying world.'],
            ['title' => 'The Kite Runner',                        'author' => 'Khaled Hosseini',            'isbn_13' => '9781594631931', 'genre' => 'Fiction',      'book_type' => 'book', 'published_year' => 2003, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'A story of friendship, betrayal, and redemption set against the backdrop of a turbulent Afghanistan.'],

            // Fantasy
            ['title' => 'The Hobbit',                             'author' => 'J.R.R. Tolkien',             'isbn_13' => '9780547928227', 'genre' => 'Fantasy',      'book_type' => 'book', 'published_year' => 1937, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'Bilbo Baggins is whisked away on an unexpected adventure with thirteen dwarves to reclaim a mountain of dragon-guarded treasure.'],
            ['title' => 'The Name of the Wind',                   'author' => 'Patrick Rothfuss',           'isbn_13' => '9780756404079', 'genre' => 'Fantasy',      'book_type' => 'book', 'published_year' => 2007, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'The story of Kvothe, a legendary figure, told in his own words — a tale of magic, music, love, and loss.'],
            ['title' => 'A Game of Thrones',                      'author' => 'George R.R. Martin',         'isbn_13' => '9780553573404', 'genre' => 'Fantasy',      'book_type' => 'book', 'published_year' => 1996, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'Noble families vie for control of the Iron Throne in a medieval world of treachery, war, and dragons.'],
            ['title' => 'The Way of Kings',                       'author' => 'Brandon Sanderson',          'isbn_13' => '9780765326355', 'genre' => 'Fantasy',      'book_type' => 'book', 'published_year' => 2010, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'Epic fantasy set on a world of magical storms, following warriors, scholars, and assassins seeking ancient secrets.'],
            ['title' => 'Mistborn: The Final Empire',             'author' => 'Brandon Sanderson',          'isbn_13' => '9780765311788', 'genre' => 'Fantasy',      'book_type' => 'book', 'published_year' => 2006, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'A thief crew plots to overthrow an immortal god-emperor in a world of ash, mists, and metal-based magic.'],

            // Mystery & Thriller
            ['title' => 'The Girl with the Dragon Tattoo',        'author' => 'Stieg Larsson',              'isbn_13' => '9780307454546', 'genre' => 'Mystery',      'book_type' => 'book', 'published_year' => 2005, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'A journalist and a hacker uncover decades-old murder and financial crime in a powerful Swedish family.'],
            ['title' => 'Gone Girl',                              'author' => 'Gillian Flynn',              'isbn_13' => '9780307588371', 'genre' => 'Mystery',      'book_type' => 'book', 'published_year' => 2012, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'A psychological thriller about a marriage gone terrifyingly wrong when Amy Dunne disappears on her anniversary.'],
            ['title' => 'The Da Vinci Code',                      'author' => 'Dan Brown',                  'isbn_13' => '9780385504201', 'genre' => 'Mystery',      'book_type' => 'book', 'published_year' => 2003, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'A murder in the Louvre sets symbologist Robert Langdon on a race through centuries of religious conspiracy.'],
            ['title' => 'And Then There Were None',               'author' => 'Agatha Christie',            'isbn_13' => '9780062073488', 'genre' => 'Mystery',      'book_type' => 'book', 'published_year' => 1939, 'total_copies' => 2, 'available_copies' => 2, 'description' => "Ten strangers lured to an isolated island begin dying one by one — the world's best-selling mystery novel."],
            ['title' => 'The Silence of the Lambs',               'author' => 'Thomas Harris',              'isbn_13' => '9780312924584', 'genre' => 'Mystery',      'book_type' => 'book', 'published_year' => 1988, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'FBI trainee Clarice Starling enlists the help of imprisoned cannibal Dr. Hannibal Lecter to catch a serial killer.'],

            // History
            ['title' => 'Guns, Germs, and Steel',                 'author' => 'Jared Diamond',              'isbn_13' => '9780393317558', 'genre' => 'History',      'book_type' => 'book', 'published_year' => 1997, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'Explores why some civilizations came to dominate others through geography, disease, and access to technology.'],
            ['title' => 'The Art of War',                         'author' => 'Sun Tzu',                    'isbn_13' => '9781599869773', 'genre' => 'History',      'book_type' => 'book', 'published_year' => 500,  'total_copies' => 3, 'available_copies' => 3, 'description' => 'Ancient Chinese military treatise on strategy, deception, and the philosophy of conflict — still studied today.'],
            ['title' => 'The Diary of a Young Girl',              'author' => 'Anne Frank',                 'isbn_13' => '9780553296983', 'genre' => 'History',      'book_type' => 'book', 'published_year' => 1947, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'The diary Anne Frank kept while hiding from the Nazis in occupied Amsterdam — one of the most powerful memoirs ever written.'],
            ['title' => 'SPQR: A History of Ancient Rome',        'author' => 'Mary Beard',                 'isbn_13' => '9781631492228', 'genre' => 'History',      'book_type' => 'book', 'published_year' => 2015, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'A bold re-examination of ancient Rome, exploring how its history shaped the world we live in today.'],

            // Business
            ['title' => 'Zero to One',                            'author' => 'Peter Thiel',                'isbn_13' => '9780804139021', 'genre' => 'Business',     'book_type' => 'book', 'published_year' => 2014, 'total_copies' => 3, 'available_copies' => 3, 'description' => "Peter Thiel's contrarian notes on startups: how to build companies that create entirely new things rather than copying."],
            ['title' => 'The Lean Startup',                       'author' => 'Eric Ries',                  'isbn_13' => '9780307887894', 'genre' => 'Business',     'book_type' => 'book', 'published_year' => 2011, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'How entrepreneurs use continuous innovation, minimum viable products, and validated learning to build sustainable businesses.'],
            ['title' => 'Rich Dad Poor Dad',                      'author' => 'Robert T. Kiyosaki',         'isbn_13' => '9781612680194', 'genre' => 'Business',     'book_type' => 'book', 'published_year' => 1997, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'A classic personal finance book contrasting the money mindsets of a rich father and a poor father.'],
            ['title' => 'The Psychology of Money',                'author' => 'Morgan Housel',              'isbn_13' => '9780857197689', 'genre' => 'Business',     'book_type' => 'book', 'published_year' => 2020, 'total_copies' => 3, 'available_copies' => 3, 'description' => "Timeless lessons on wealth, greed, and happiness — how people's behavior with money matters more than knowledge."],
            ['title' => 'Good to Great',                          'author' => 'Jim Collins',                'isbn_13' => '9780066620992', 'genre' => 'Business',     'book_type' => 'book', 'published_year' => 2001, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'A research-backed study of what separates companies that make the leap from good results to sustained greatness.'],

            // Horror
            ['title' => 'It',                                     'author' => 'Stephen King',               'isbn_13' => '9781501156700', 'genre' => 'Horror',       'book_type' => 'book', 'published_year' => 1986, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'A group of childhood friends must confront a shape-shifting evil entity that exploits their deepest fears.'],
            ['title' => 'The Shining',                            'author' => 'Stephen King',               'isbn_13' => '9780385121675', 'genre' => 'Horror',       'book_type' => 'book', 'published_year' => 1977, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'A struggling writer and his family caretake a haunted hotel over winter, descending into supernatural terror.'],
            ['title' => 'Dracula',                                'author' => 'Bram Stoker',                'isbn_13' => '9780141439846', 'genre' => 'Horror',       'book_type' => 'book', 'published_year' => 1897, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'The definitive Gothic horror novel introducing Count Dracula — told through letters, diaries, and newspaper clippings.'],
            ['title' => 'Frankenstein',                           'author' => 'Mary Shelley',               'isbn_13' => '9780141439471', 'genre' => 'Horror',       'book_type' => 'book', 'published_year' => 1818, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'A scientist creates life from dead tissue and faces the catastrophic moral consequences of playing God.'],

            // Romance
            ['title' => 'The Notebook',                           'author' => 'Nicholas Sparks',            'isbn_13' => '9780446605236', 'genre' => 'Romance',      'book_type' => 'book', 'published_year' => 1996, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'A timeless love story of a poor young man and a wealthy young woman who find each other one summer and never let go.'],
            ['title' => 'Me Before You',                          'author' => 'Jojo Moyes',                 'isbn_13' => '9780143124542', 'genre' => 'Romance',      'book_type' => 'book', 'published_year' => 2012, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'A young woman takes a job caring for a paralyzed man and their unexpected bond changes both their lives forever.'],
            ['title' => 'Outlander',                              'author' => 'Diana Gabaldon',             'isbn_13' => '9780440212560', 'genre' => 'Romance',      'book_type' => 'book', 'published_year' => 1991, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'A WWII nurse is hurled back in time to 18th-century Scotland and falls in love with a Highland warrior.'],
            ['title' => 'It Ends with Us',                        'author' => 'Colleen Hoover',             'isbn_13' => '9781501110368', 'genre' => 'Romance',      'book_type' => 'book', 'published_year' => 2016, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'A woman builds her dream business and falls in love, but must face difficult choices about her own past and future.'],

            // Biography
            ['title' => 'Steve Jobs',                             'author' => 'Walter Isaacson',            'isbn_13' => '9781451648539', 'genre' => 'Biography',    'book_type' => 'book', 'published_year' => 2011, 'total_copies' => 3, 'available_copies' => 3, 'description' => "The authorized biography of Apple's visionary co-founder, based on over forty exclusive interviews with Jobs."],
            ['title' => 'Educated',                               'author' => 'Tara Westover',              'isbn_13' => '9780399590504', 'genre' => 'Biography',    'book_type' => 'book', 'published_year' => 2018, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'A woman who grew up in a survivalist family in rural Idaho eventually earns a PhD from Cambridge University.'],
            ['title' => 'Long Walk to Freedom',                   'author' => 'Nelson Mandela',             'isbn_13' => '9780316548182', 'genre' => 'Biography',    'book_type' => 'book', 'published_year' => 1994, 'total_copies' => 2, 'available_copies' => 2, 'description' => "The autobiography of South Africa's first democratically elected president and lifelong anti-apartheid activist."],

            // Philosophy
            ['title' => 'Meditations',                            'author' => 'Marcus Aurelius',            'isbn_13' => '9780140449334', 'genre' => 'Philosophy',   'book_type' => 'book', 'published_year' => 180,  'total_copies' => 3, 'available_copies' => 3, 'description' => 'Personal writings of the Roman emperor, reflecting Stoic philosophy on duty, impermanence, and virtue.'],
            ['title' => 'Thus Spoke Zarathustra',                 'author' => 'Friedrich Nietzsche',        'isbn_13' => '9780140441185', 'genre' => 'Philosophy',   'book_type' => 'book', 'published_year' => 1883, 'total_copies' => 2, 'available_copies' => 2, 'description' => "Nietzsche's poetic philosophical novel introducing concepts like the Übermensch and the will to power."],
            ['title' => 'The Republic',                           'author' => 'Plato',                      'isbn_13' => '9780140455113', 'genre' => 'Philosophy',   'book_type' => 'book', 'published_year' => 380,  'total_copies' => 2, 'available_copies' => 2, 'description' => 'Socratic dialogues exploring justice, the ideal state, and the role of the philosopher-king in society.'],
            ['title' => 'Beyond Good and Evil',                   'author' => 'Friedrich Nietzsche',        'isbn_13' => '9780140449235', 'genre' => 'Philosophy',   'book_type' => 'book', 'published_year' => 1886, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'A critique of past philosophers and a call to re-examine moral values beyond simple good and evil dichotomies.'],
        ];

        foreach ($books as $book) {
            $book['isbn_10']     = $this->toIsbn10($book['isbn_13']);
            $book['cover_image'] = "https://covers.openlibrary.org/b/isbn/{$book['isbn_13']}-L.jpg";
            Book::firstOrCreate(['isbn_13' => $book['isbn_13']], $book);
        }
    }
}