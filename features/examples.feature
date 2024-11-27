Feature: Examples of usage

  Scenario: Querying
    Given the content of table 'vehicle' is
      | id        | mark    | color  | price       |
      | %int(5)%  | Peugeot | Red    | %int(1550)% |
      | %int(14)% | Peugeot | Green  | %int(1200)% |
      | %int(22)% | Peugeot | Blue   | %int(1400)% |
      | %int(31)% | Citroen | Red    | %int(2000)% |
      | %int(45)% | Citroen | Yellow | %int(1800)% |
      | %int(52)% | Citroen | Brown  | %int(1400)% |
      | %int(67)% | Nissan  | Brown  | %int(1700)% |
      | %int(71)% | Nissan  | Green  | %int(1750)% |
      | %int(89)% | Nissan  | Red    | %int(1500)% |

    Given the find into 'vehicle' of 'price' > 1500 is
      | id        | mark    | color  | price       |
      | %int(5)%  | Peugeot | Red    | %int(1550)% |
      | %int(31)% | Citroen | Red    | %int(2000)% |
      | %int(45)% | Citroen | Yellow | %int(1800)% |
      | %int(67)% | Nissan  | Brown  | %int(1700)% |
      | %int(71)% | Nissan  | Green  | %int(1750)% |

    Given the find into 'vehicle' of 'price' > 1500 AND 'color' = 'Red' is
      | id        | mark    | color | price       |
      | %int(5)%  | Peugeot | Red   | %int(1550)% |
      | %int(31)% | Citroen | Red   | %int(2000)% |

    Given the find into 'vehicle' of 'price' > 1500 OR 'color' = 'Red' is
      | id        | mark    | color  | price       |
      | %int(5)%  | Peugeot | Red    | %int(1550)% |
      | %int(31)% | Citroen | Red    | %int(2000)% |
      | %int(45)% | Citroen | Yellow | %int(1800)% |
      | %int(67)% | Nissan  | Brown  | %int(1700)% |
      | %int(71)% | Nissan  | Green  | %int(1750)% |
      | %int(89)% | Nissan  | Red    | %int(1500)% |

    Given the find into 'vehicle' of 'price' > 1500 OR 'color' = 'Red' SORT BY 'mark' asc then 'price' desc is
      | id        | mark    | color  | price       |
      | %int(31)% | Citroen | Red    | %int(2000)% |
      | %int(45)% | Citroen | Yellow | %int(1800)% |
      | %int(71)% | Nissan  | Green  | %int(1750)% |
      | %int(67)% | Nissan  | Brown  | %int(1700)% |
      | %int(89)% | Nissan  | Red    | %int(1500)% |
      | %int(5)%  | Peugeot | Red    | %int(1550)% |

    Given the find into 'vehicle' of 'price' > 1500 OR 'color' = 'Red' SORT BY 'mark' asc then 'price' desc LIMIT 1, 3 ==> find 'color' match 'w' SORT BY 'price' asc is
      | id        | mark    | color  | price       |
      | %int(67)% | Nissan  | Brown  | %int(1700)% |
      | %int(45)% | Citroen | Yellow | %int(1800)% |

    Given the find into 'vehicle' of ('price' > 1600 AND 'color' = 'Red') OR ('price' < 1600 AND 'color' = 'Green') is
      | id        | mark    | color | price       |
      | %int(14)% | Peugeot | Green | %int(1200)% |
      | %int(31)% | Citroen | Red   | %int(2000)% |

    Given the find into 'vehicle' of 'id' IN (52,31,89) SORT BY 'price' asc is
      | id        | mark    | color | price       |
      | %int(52)% | Citroen | Brown | %int(1400)% |
      | %int(89)% | Nissan  | Red   | %int(1500)% |
      | %int(31)% | Citroen | Red   | %int(2000)% |

    Given the find into 'vehicle' of {key} IN (52,31,89) SORT BY 'price' asc is
      | id        | mark    | color | price       |
      | %int(52)% | Citroen | Brown | %int(1400)% |
      | %int(89)% | Nissan  | Red   | %int(1500)% |
      | %int(31)% | Citroen | Red   | %int(2000)% |


  Scenario: Updating / Deleting
    Given the content of table 'vehicle' is
      | id        | mark    | color  | price       |
      | %int(5)%  | Peugeot | Red    | %int(1550)% |
      | %int(14)% | Peugeot | Green  | %int(1200)% |
      | %int(22)% | Peugeot | Blue   | %int(1400)% |
      | %int(31)% | Citroen | Red    | %int(2000)% |
      | %int(45)% | Citroen | Yellow | %int(1800)% |
      | %int(52)% | Citroen | Brown  | %int(1400)% |
      | %int(67)% | Nissan  | Brown  | %int(1700)% |
      | %int(71)% | Nissan  | Green  | %int(1750)% |
      | %int(89)% | Nissan  | Red    | %int(1500)% |

    Then the update of 'price' > 1500 => 'price' / 10 gives
      | id        | mark    | color  | price       |
      | %int(5)%  | Peugeot | Red    | %int(155)%  |
      | %int(14)% | Peugeot | Green  | %int(1200)% |
      | %int(22)% | Peugeot | Blue   | %int(1400)% |
      | %int(31)% | Citroen | Red    | %int(200)%  |
      | %int(45)% | Citroen | Yellow | %int(180)%  |
      | %int(52)% | Citroen | Brown  | %int(1400)% |
      | %int(67)% | Nissan  | Brown  | %int(170)%  |
      | %int(71)% | Nissan  | Green  | %int(175)%  |
      | %int(89)% | Nissan  | Red    | %int(1500)% |

    Then we delete 'price' < 180 OR 'color' = 'brown'
      | id        | mark    | color  | price       |
      | %int(14)% | Peugeot | Green  | %int(1200)% |
      | %int(22)% | Peugeot | Blue   | %int(1400)% |
      | %int(31)% | Citroen | Red    | %int(200)%  |
      | %int(45)% | Citroen | Yellow | %int(180)%  |
      | %int(89)% | Nissan  | Red    | %int(1500)% |

    Then we update the 'price' with 2000 for 'id' = 45
      | id        | mark    | color  | price       |
      | %int(14)% | Peugeot | Green  | %int(1200)% |
      | %int(22)% | Peugeot | Blue   | %int(1400)% |
      | %int(31)% | Citroen | Red    | %int(200)%  |
      | %int(45)% | Citroen | Yellow | %int(2000)% |
      | %int(89)% | Nissan  | Red    | %int(1500)% |

    Then we update the 'price' with 2100 for {key} = 45
      | id        | mark    | color  | price       |
      | %int(14)% | Peugeot | Green  | %int(1200)% |
      | %int(22)% | Peugeot | Blue   | %int(1400)% |
      | %int(31)% | Citroen | Red    | %int(200)%  |
      | %int(45)% | Citroen | Yellow | %int(2100)% |
      | %int(89)% | Nissan  | Red    | %int(1500)% |

    Then we insert the row '{"mark": "BMW", "price": 3000, "color": "Green"}'
      | id        | mark    | color  | price       |
      | %int(14)% | Peugeot | Green  | %int(1200)% |
      | %int(22)% | Peugeot | Blue   | %int(1400)% |
      | %int(31)% | Citroen | Red    | %int(200)%  |
      | %int(45)% | Citroen | Yellow | %int(2100)% |
      | %int(89)% | Nissan  | Red    | %int(1500)% |
      | %int(90)% | BMW     | Green  | %int(3000)% |

    Then we upsert the row '{"mark": "BMW", "price": 3100, "color": "Red"}'
      | id        | mark    | color  | price       |
      | %int(14)% | Peugeot | Green  | %int(1200)% |
      | %int(22)% | Peugeot | Blue   | %int(1400)% |
      | %int(31)% | Citroen | Red    | %int(200)%  |
      | %int(45)% | Citroen | Yellow | %int(2100)% |
      | %int(89)% | Nissan  | Red    | %int(1500)% |
      | %int(90)% | BMW     | Green  | %int(3000)% |
      | %int(91)% | BMW     | Red    | %int(3100)% |

    Then we upsert the last inserted row with '{"color": "Yellow"}'
      | id        | mark    | color  | price       |
      | %int(14)% | Peugeot | Green  | %int(1200)% |
      | %int(22)% | Peugeot | Blue   | %int(1400)% |
      | %int(31)% | Citroen | Red    | %int(200)%  |
      | %int(45)% | Citroen | Yellow | %int(2100)% |
      | %int(89)% | Nissan  | Red    | %int(1500)% |
      | %int(90)% | BMW     | Green  | %int(3000)% |
      | %int(91)% | BMW     | Yellow | %int(3100)% |

    Then we single delete the {key} 45
      | id        | mark    | color  | price       |
      | %int(14)% | Peugeot | Green  | %int(1200)% |
      | %int(22)% | Peugeot | Blue   | %int(1400)% |
      | %int(31)% | Citroen | Red    | %int(200)%  |
      | %int(89)% | Nissan  | Red    | %int(1500)% |
      | %int(90)% | BMW     | Green  | %int(3000)% |
      | %int(91)% | BMW     | Yellow | %int(3100)% |


  Scenario: Foreign tables
    Given the content of table 'color' is
      | id       | name   |
      | %int(1)% | Red    |
      | %int(2)% | Green  |
      | %int(3)% | Blue   |
      | %int(4)% | Yellow |
      | %int(5)% | Brown  |

    Given the content of table 'mark' is
      | id       | name    |
      | %int(1)% | Peugeot |
      | %int(2)% | Citroen |
      | %int(3)% | Nissan  |

    Given the content of table 'vehicle_linked' is
      | id        | mark_id  | color_id | price       |
      | %int(5)%  | %int(1)% | %int(1)% | %int(1550)% |
      | %int(14)% | %int(1)% | %int(2)% | %int(1200)% |
      | %int(22)% | %int(1)% | %int(3)% | %int(1400)% |
      | %int(31)% | %int(2)% | %int(1)% | %int(2000)% |
      | %int(45)% | %int(2)% | %int(4)% | %int(1800)% |
      | %int(52)% | %int(2)% | %int(5)% | %int(1400)% |
      | %int(67)% | %int(3)% | %int(5)% | %int(1700)% |
      | %int(71)% | %int(3)% | %int(2)% | %int(1750)% |
      | %int(89)% | %int(3)% | %int(1)% | %int(1500)% |

    Then all 'vehicle_linked' with corresponding names gives
      | id        | mark_id  | mark_name | color  | price       |
      | %int(5)%  | %int(1)% | Peugeot   | Red    | %int(1550)% |
      | %int(14)% | %int(1)% | Peugeot   | Green  | %int(1200)% |
      | %int(22)% | %int(1)% | Peugeot   | Blue   | %int(1400)% |
      | %int(31)% | %int(2)% | Citroen   | Red    | %int(2000)% |
      | %int(45)% | %int(2)% | Citroen   | Yellow | %int(1800)% |
      | %int(52)% | %int(2)% | Citroen   | Brown  | %int(1400)% |
      | %int(67)% | %int(3)% | Nissan    | Brown  | %int(1700)% |
      | %int(71)% | %int(3)% | Nissan    | Green  | %int(1750)% |
      | %int(89)% | %int(3)% | Nissan    | Red    | %int(1500)% |

    Then the 'vehicle_linked' filtered on color name 'Red' without displaying it gives
      | id        | mark_id  | color_id | price       |
      | %int(5)%  | %int(1)% | %int(1)% | %int(1550)% |
      | %int(31)% | %int(2)% | %int(1)% | %int(2000)% |
      | %int(89)% | %int(3)% | %int(1)% | %int(1500)% |

    Then the 'vehicle_linked' with multiple filters (color = 'Red' AND mark matches 'o') gives
      | id        | mark_id  | color_id | price       |
      | %int(5)%  | %int(1)% | %int(1)% | %int(1550)% |
      | %int(31)% | %int(2)% | %int(1)% | %int(2000)% |
