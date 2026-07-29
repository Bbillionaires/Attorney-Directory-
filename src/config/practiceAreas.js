// Static marketing copy for the "Browse the Full Legal Menu" homepage cards.
// Keyed by the exact categories.name value so it can be joined to real category rows at render time.
const PRACTICE_AREAS = [
  {
    categoryName: 'Personal Injury',
    tagline: 'Legal help after an accident or injury.',
    bullets: [
      'Car accidents', 'Truck accidents', 'Motorcycle accidents', 'Slip-and-fall injuries',
      'Wrongful death', 'Medical malpractice', 'Workplace injuries', 'Product liability',
    ],
    ctaLabel: 'View Personal Injury',
  },
  {
    categoryName: 'Criminal Defense',
    tagline: 'Representation for criminal charges and investigations.',
    bullets: [
      'DUI and DWI', 'Misdemeanors', 'Felonies', 'Drug charges',
      'Theft charges', 'Assault charges', 'Probation violations', 'Record sealing and expungement',
    ],
    ctaLabel: 'View Criminal Defense',
  },
  {
    categoryName: 'Family Law',
    tagline: 'Guidance for family and relationship matters.',
    bullets: [
      'Divorce', 'Child custody', 'Child support', 'Alimony',
      'Adoption', 'Paternity', 'Guardianship', 'Domestic violence',
    ],
    ctaLabel: 'View Family Law',
  },
  {
    categoryName: 'Business Law',
    tagline: 'Legal services for businesses, owners, and entrepreneurs.',
    bullets: [
      'Business formation', 'Contracts', 'Partnership disputes', 'Employment matters',
      'Commercial litigation', 'Business purchases and sales', 'Compliance', 'Intellectual property',
    ],
    ctaLabel: 'View Business Law',
  },
  {
    categoryName: 'Real Estate Law',
    tagline: 'Legal assistance for residential and commercial property matters.',
    bullets: [
      'Closings', 'Title issues', 'Landlord and tenant disputes', 'Foreclosures',
      'Property disputes', 'Purchase agreements', 'Commercial leases', 'Construction disputes',
    ],
    ctaLabel: 'View Real Estate Law',
  },
  {
    categoryName: 'Estate Planning',
    tagline: 'Plan, protect, and prepare for the future.',
    bullets: [
      'Wills', 'Trusts', 'Probate', 'Powers of attorney',
      'Guardianship planning', 'Asset protection', 'Estate administration', 'Healthcare directives',
    ],
    ctaLabel: 'View Estate Planning',
  },
  {
    categoryName: 'Immigration Law',
    tagline: 'Legal guidance for individuals, families, and employers.',
    bullets: [
      'Green cards', 'Citizenship', 'Work visas', 'Family petitions',
      'Deportation defense', 'Asylum', 'Student visas', 'Employer immigration matters',
    ],
    ctaLabel: 'View Immigration Law',
  },
  {
    categoryName: 'Bankruptcy and Debt',
    tagline: 'Explore legal options for overwhelming debt and creditor pressure.',
    bullets: [
      'Chapter 7 bankruptcy', 'Chapter 13 bankruptcy', 'Foreclosure defense', 'Wage garnishment',
      'Creditor harassment', 'Debt lawsuits', 'Repossessions', 'Business bankruptcy',
    ],
    ctaLabel: 'View Bankruptcy Options',
  },
  {
    categoryName: 'Employment Law',
    tagline: 'Legal help for workplace disputes.',
    bullets: [
      'Wrongful termination', 'Discrimination', 'Harassment', 'Unpaid wages',
      'Retaliation', 'Employment contracts', 'Workplace injuries', 'Whistleblower matters',
    ],
    ctaLabel: 'View Employment Law',
  },
];

// The 3 "Today's Specials" featured cards on the homepage hero section (subset of the above, extra copy).
const FEATURED_SERVICES = [
  {
    categoryName: 'Personal Injury',
    heading: 'Car Accident Claims',
    intro: 'Injured in a car accident caused by someone else?',
    description: 'Review your options for medical expenses, lost income, vehicle damage, pain and suffering, and insurance disputes.',
    bullets: ['Rear-end accidents', 'Rideshare accidents', 'Uninsured drivers', 'Hit-and-run claims', 'Commercial vehicle accidents'],
    ctaLabel: 'Start My Case Review',
  },
  {
    categoryName: 'Criminal Defense',
    heading: 'Criminal Defense',
    intro: 'Facing an arrest, criminal charge, investigation, or court appearance?',
    description: 'Connect with an attorney who handles cases involving misdemeanors, felonies, DUI charges, probation issues, and other criminal matters.',
    bullets: [],
    ctaLabel: 'Find a Defense Attorney',
  },
  {
    categoryName: 'Family Law',
    heading: 'Family Law',
    intro: 'Legal support for personal and family matters.',
    description: '',
    bullets: ['Divorce', 'Child custody', 'Child support', 'Adoption', 'Paternity', 'Domestic violence matters'],
    ctaLabel: 'Explore Family Law',
  },
];

const POPULAR_CHOICES = [
  'Car Accident Attorney', 'Criminal Defense Attorney', 'Divorce Attorney', 'Child Custody Attorney',
  'Bankruptcy Attorney', 'Immigration Attorney', 'Business Attorney', 'Real Estate Attorney',
];

const LEGAL_RESOURCES = [
  'Questions to ask an attorney', 'How attorney fees may work', 'What to bring to a consultation',
  'How legal referrals work', 'Understanding contingency fees', 'Preparing a case timeline',
  'Gathering important documents', 'Legal terms explained',
];

module.exports = { PRACTICE_AREAS, FEATURED_SERVICES, POPULAR_CHOICES, LEGAL_RESOURCES };
