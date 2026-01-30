import { Backend } from "./backend.js";

class User {
    static Instances = [];

    static async loadAll() {
      return Backend.bind()
        .then( () => {
          let binding = Backend.getBinding();
          User.save = binding.usersSaver;
          return binding.usersLoader(); } )
        .then( rawData => this.createInstances(rawData) );
    }

  static getAll() {
    this.Instances.sort( (a,b) => a.id - b.id );
    return this.Instances; 
  }

  constructor(name, password='', role='') {
    this.id = 0;
    this.name = name;
    this.password = password;
    this.role = role;
  }

  static createInstances(rawData) {
    this.Instances = [];
    rawData.forEach( (user) => {
      let newUser = new User(user.name, '', user.role);
      newUser.id = user.id;
      this.Instances.push(newUser);
    });
  }

  save() {
    return User.save( this.id==0 ? 'create' : 'update', this)
    .then( result => User.loadAll()
    .then( () => result ) );
  }

  delete() {
    return User.save( 'delete', this)
    .then( result => User.loadAll()
    .then( () => result ) );
  }

}

export { User };